<?php

/* core/themes/seven/templates/tablesort-indicator.html.twig */
class __TwigTemplate_b7c6a65ffc13c7757429cc528fd4a26dcd716efabb3a164c27bf85772cd7dae2 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 12
        if (((isset($context["style"]) ? $context["style"] : null) == "asc")) {
            // line 13
            echo "<img src=\"";
            echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, (isset($context["arrow_asc"]) ? $context["arrow_asc"] : null), "html", null, true);
            echo "\" width=\"9\" height=\"5\" alt=\"";
            echo $this->env->getExtension('drupal_core')->renderVar(t("Sort ascending"));
            echo "\" title=\"";
            echo $this->env->getExtension('drupal_core')->renderVar(t("Sort ascending"));
            echo "\" />
";
        } else {
            // line 15
            echo "<img src=\"";
            echo $this->env->getExtension('drupal_core')->escapeFilter($this->env, (isset($context["arrow_desc"]) ? $context["arrow_desc"] : null), "html", null, true);
            echo "\" width=\"9\" height=\"5\" alt=\"";
            echo $this->env->getExtension('drupal_core')->renderVar(t("Sort descending"));
            echo "\" title=\"";
            echo $this->env->getExtension('drupal_core')->renderVar(t("Sort descending"));
            echo "\" />
";
        }
    }

    public function getTemplateName()
    {
        return "core/themes/seven/templates/tablesort-indicator.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 15,  21 => 13,  19 => 12,);
    }
}
