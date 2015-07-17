<?php

/**
 * @file
 * Contains \Drupal\statistics\Plugin\Block\StatisticsPopularBlock.
 */

namespace Drupal\statistics\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Cache\Cache;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\statistics\StatisticsStorageInterface;

/**
 * Provides a 'Popular content' block.
 *
 * @Block(
 *   id = "statistics_popular_block",
 *   admin_label = @Translation("Popular content")
 * )
 */
class StatisticsPopularBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The storage for statistics.
   *
   * @var \Drupal\statistics\StatisticsStorageInterface
   */
  protected $statisticsStorage;

  /**
   * Number of day's top views to display.
   *
   * @var int
   */
  protected $day_list;

  /**
   * Number of all time views to display.
   *
   * @var int
   */
  protected $all_time_list;

  /**
   * Number of most recent views to display.
   *
   * @var int
   */
  protected $last_list;

  /**
   * Constructs an StatisticsPopularBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   * @param \Drupal\statistics\StatisticsStorageInterface $statistics_storage
   *   The storage for statistics.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityManagerInterface $entity_manager, StatisticsStorageInterface $statistics_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityManager = $entity_manager;
    $this->statisticsStorage = $statistics_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.manager'),
      $container->get('statistics.statistics_storage')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'top_day_num' => 0,
      'top_all_num' => 0,
      'top_last_num' => 0
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    $access = AccessResult::allowedIfHasPermission($account, 'access content');
    if ($account->hasPermission('access content')) {
      $daytop = $this->configuration['top_day_num'];
      if (!$daytop || !($this->day_list = $this->statisticsStorage->fetchAll('daycount', $daytop))) {
        return AccessResult::forbidden()->inheritCacheability($access);
      }
      $alltimetop = $this->configuration['top_all_num'];
      if (!$alltimetop || !($this->all_time_list = $this->statisticsStorage->fetchAll('totalcount', $alltimetop))) {
        return AccessResult::forbidden()->inheritCacheability($access);
      }
      $lasttop = $this->configuration['top_last_num'];
      if (!$lasttop || !($this->last_list = $this->statisticsStorage->fetchAll('timestamp', $lasttop))) {
        return AccessResult::forbidden()->inheritCacheability($access);
      }
      return $access;
    }
    return AccessResult::forbidden()->inheritCacheability($access);
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    // Popular content block settings.
    $numbers = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 15, 20, 25, 30, 40);
    $numbers = array('0' => $this->t('Disabled')) + array_combine($numbers, $numbers);
    $form['statistics_block_top_day_num'] = array(
     '#type' => 'select',
     '#title' => $this->t("Number of day's top views to display"),
     '#default_value' => $this->configuration['top_day_num'],
     '#options' => $numbers,
     '#description' => $this->t('How many content items to display in "day" list.'),
    );
    $form['statistics_block_top_all_num'] = array(
      '#type' => 'select',
      '#title' => $this->t('Number of all time views to display'),
      '#default_value' => $this->configuration['top_all_num'],
      '#options' => $numbers,
      '#description' => $this->t('How many content items to display in "all time" list.'),
    );
    $form['statistics_block_top_last_num'] = array(
      '#type' => 'select',
      '#title' => $this->t('Number of most recent views to display'),
      '#default_value' => $this->configuration['top_last_num'],
      '#options' => $numbers,
      '#description' => $this->t('How many content items to display in "recently viewed" list.'),
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['top_day_num'] = $form_state->getValue('statistics_block_top_day_num');
    $this->configuration['top_all_num'] = $form_state->getValue('statistics_block_top_all_num');
    $this->configuration['top_last_num'] = $form_state->getValue('statistics_block_top_last_num');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $content = array();

    if ($this->day_list) {
      $content['top_day'] = $this->nodeTitleList($this->day_list, $this->t("Today's:"));
      $content['top_day']['#suffix'] = '<br />';
    }

    if ($this->all_time_list) {
      $content['top_all'] = $this->nodeTitleList($this->all_time_list, $this->t("All time:"));
      $content['top_all']['#suffix'] = '<br />';
    }

    if ($this->last_list) {
      $content['top_last'] = $this->nodeTitleList($this->last_list, $this->t("Last viewed:"));
      $content['top_last']['#suffix'] = '<br />';
    }

    return $content;
  }

  protected function nodeTitleList($counts, $title) {
    $nodes = $this->entityManager->getStorage('node')->loadMultiple($counts);

    $items = array();
    foreach ($counts as $count) {
      // $items [] = \Drupal::l($nodes[$count]->getTitle(), $nodes[$count]->urlInfo('canonical'));
      $items [] = array(
        '#type' => 'link',
        '#title' => $nodes[$count]->getTitle(),
        '#url' => $nodes[$count]->urlInfo('canonical'),
      )
    }

    return array(
      '#theme' => 'item_list__node',
      '#items' => $items,
      '#title' => $title,
      '#cache' => ['tags' => Cache::mergeTags(['node_list'], Cache::buildTags('node', $counts))]
      );
  }

}
