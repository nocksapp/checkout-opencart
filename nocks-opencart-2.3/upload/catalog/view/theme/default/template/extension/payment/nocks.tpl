<div class="buttons" style="flex-direction:column;display:flex;align-items:flex-end;">
    <?php if (!empty($issuers)) { ?>
    <div style="margin-bottom:1em">
        <select name="nocks_ideal_issuer" id="nocks_ideal_issuer">
            <option value=""><?php echo $text_select_your_bank; ?></option>
            <?php foreach ($issuers as $key => $label) { ?>
            <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
            <?php } ?>
        </select>
    </div>
    <?php } ?>
    <div>
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
    </div>
</div>
<script type="text/javascript">
    $('#button-confirm').on('click', function() {
        $.ajax({
            url: 'index.php?route=<?php echo $redirect_route; ?>',
            data: {
                issuer: $('#nocks_ideal_issuer').val()
            },
            dataType: 'json',
            beforeSend: function() {
                $('#button-confirm').button('loading');
            },
            complete: function() {
                $('#button-confirm').button('reset');
            },
            success: function(json) {
                if (json['redirect']) {
                    location = json['redirect'];
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
</script>