<?php
function validate_cc_number($cc_number) {
   /* Validate; return value is card type if valid. */
   $false = false;
   $card_type = "";
   $card_regexes = array(
      "/^4\d{12}(\d\d\d){0,1}$/" => "VISA",
      "/^5[12345]\d{14}$/"       => "MASTERCARD",
//      "/^3[47]\d{13}$/"          => "AMEX",
//      "/^6011\d{12}$/"           => "discover",
//      "/^30[012345]\d{11}$/"     => "diners",
//      "/^3[68]\d{12}$/"          => "diners",
   );

   foreach ($card_regexes as $regex => $type) {
       if (preg_match($regex, $cc_number)) {
           $card_type = $type;
           break;
       }
   }

   if (!$card_type) {
       return $false;
   }

   /*  mod 10 checksum algorithm  */
   $revcode = strrev($cc_number);
   $checksum = 0; 

   for ($i = 0; $i < strlen($revcode); $i++) {
       $current_num = intval($revcode[$i]);  
       if($i & 1) {  /* Odd  position */
          $current_num *= 2;
       }
       /* Split digits and add. */
           $checksum += $current_num % 10; if
       ($current_num >  9) {
           $checksum += 1;
       }
   }

   if ($checksum % 10 == 0) {
       return $card_type;
   } else {
       return $false;
   }
}

?>
