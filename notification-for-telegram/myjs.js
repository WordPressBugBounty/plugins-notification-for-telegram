jQuery(document).ready(function($){

  // TEST BUTTON - abilita solo se token e chatid sono validi
  var button = $('#buttonTest');
  var input1 = $('#token_0');
  var input2 = $('#chatids_');
  if (input1.length && input2.length && button.length) {
    button.prop('disabled', true);
    button.text('INSERT VALID TOKEN AND CHATIDs SAVE BEFORE TEST');
    input1.on('input', checkInputs);
    input2.on('input', checkInputs);
    checkInputs();
    function checkInputs() {
      var tokenLength  = input1.val().length;
      var chatidLength = input2.val().length;
      if (tokenLength > 14 && chatidLength > 5) {
        button.prop('disabled', false);
        button.text('TEST');
      } else {
        button.prop('disabled', true);
        button.text('INSERT VALID TOKEN AND CHATIDs SAVE BEFORE TEST');
      }
    }
  }

  
});