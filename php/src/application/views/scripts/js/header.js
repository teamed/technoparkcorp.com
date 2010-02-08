/**
 *
 *
 */
 
function selector(span, title)
{
    $(document).attr('cursor', 'waiting');
    // send AJAX request to the server to get the list of all options
    $.ajax(
        {
            url: "<?=$this->url(array('action'=>'opts'), 'pf', true)?>",
            type: 'POST',
            data: {'title': title},
            dataType: 'json',
            success: function(json)
            {
                $(document).attr('cursor', 'normal');
                // new form to be created for country selection
                var form = $(document.createElement('form'))
                    .attr('name', 'opt')
                    .attr('action', "<?=$this->url(array('action'=>'redirector'), 'pf', true)?>")
                    .attr('method' , 'post');

                // list of options
                var list = $(document.createElement('select'))
                    .bind(
                        'change blur', 
                        function() 
                        {
                            document.opt.submit();
                        }
                    )
                    .attr('name', 'document');

                // add all options to the list
                var counter = 0;
                for (code in json) {
                    // create new OPTION html element
                    var option = $(document.createElement('option'))
                        .html(json[code])
                        .val(code);

                    // if the code of this option is active - select it in the list
                    if (code == title) {
                        option.attr('selected', 'selected');
                    }

                    // append the option to the list
                    list.append(option);
                    counter++;
                };

                if (counter < 2) {
                    return;
                }

                // add list to the form
                form.append(list);

                // replace current content of the DIV by the form
                span.empty().append(form);
            }
        }
    );
}
