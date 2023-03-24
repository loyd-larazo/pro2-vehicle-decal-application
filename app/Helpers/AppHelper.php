<?php

function getNextCode($code) {
  $charSet = ["0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
  $charSetLen = count($charSet);

  if (!$code) {
    return "00000";
  }

  $newCode = "";
  $increaseNext = false;
  for ($i = (strlen($code) - 1); $i >= 0; $i--) {
    // last char
    if (($i + 1) == strlen($code)) {
      $char = $code[$i];
      $charIndex = array_search($char, $charSet);
      if (($charIndex + 1) == $charSetLen) {
        // reset to first and increase next
        $increaseNext = true;
        $newCode = $charSet[0] . $newCode;
      } else {
        $increaseNext = false;
        $newCode = $charSet[$charIndex + 1] . $newCode;
      }
    } else {
      $nextIndex = array_search($code[$i], $charSet);
      if ($increaseNext) {
        $nextIndex = $nextIndex + 1;
        if ($nextIndex == $charSetLen) {
          $nextIndex = 0;
          $increaseNext = true;
        } else {
          $increaseNext = false;
        }
      }

      $newCode = $charSet[$nextIndex] . $newCode;
    }
  }

  return $newCode;
}