<?php



class JHTMLInput{

   /**
    * Displays a checkbox
    *
    * @static
    * @return html
    *
    */
   function checkbox( $name, $label, $value ){
      
      $html = '<input type="checkbox" name="' . $name . '" value="' . $value . '" id="' . $name . '" />';
      $html .= '&nbsp;<label for="' . $name . '">' . $label . '</label>';
      return $html;
   }
   
}

