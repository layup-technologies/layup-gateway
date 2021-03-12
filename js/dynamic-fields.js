
var wrapper         = $(".input_fields_wrap"); //Fields wrapper

var add_button      = $(".add_field_button"); //Add button ID


$(add_button).click(function(e){ //on add input button click


    e.preventDefault();


        $(wrapper).append('<p class="form-field date_field_type"><span class="wrap"><label>Departure/Event Date</label><input placeholder="Date" class="" type="date" name="layup_date[]" value=""  style="width: 150px;" /></span><a href="#" class="remove_field">Remove</a></p>'); //add input box

});

$(wrapper).on("click",".remove_field", function(e){ //user click on remove text

    e.preventDefault(); 

    $(this).parent('p').remove();

});