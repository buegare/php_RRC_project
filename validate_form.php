<?php

  function isFieldEmpty($field) {
    return strlen($field) === 0;
  }

  function sanitizeString($string) {
    return trim(filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
  }

  function validateInt($int) {
    return filter_var($int, FILTER_VALIDATE_INT);
  }

  function validateField($field, $isString = true, $options = null) {
    if($isString) {
      if(isFieldEmpty(trim(filter_var($field, FILTER_SANITIZE_FULL_SPECIAL_CHARS)))) {
        return "ERROR";
      }
    } else {
      if(!filter_var($field, FILTER_VALIDATE_INT, $options)) {
        return "ERROR";
      }
    }
  }

?>