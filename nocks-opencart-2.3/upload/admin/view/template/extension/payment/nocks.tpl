<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-nocks" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-nocks" class="form-horizontal">
            <?php foreach ($shops as $shop) { ?>
            <?php if ($error_warning) { ?>
            <div class="alert alert-danger alert-dismissable">
                <i class="fa fa-exclamation-circle"></i>
                <?php echo $shop['name']; ?>: <?php echo $error_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;
                </button>
            </div>
            <?php } ?>
            <?php } ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $nocks_config_title; ?></h3>
                </div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <?php foreach ($shops as $shop) { ?>
                        <li class="<?php echo $shop['id'] === 0 ? 'active' : ''; ?>"><a data-toggle="tab" href="#store<?php echo $shop['id']; ?>"><?php echo $shop['name']; ?></a></li>
                        <?php } ?>
                    </ul>

                    <div class="tab-content">
                        <?php foreach ($shops as $shop) { ?>
                        <div id="store<?php echo $shop['id']; ?>" class="tab-pane fade in <?php echo $shop['id'] === 0 ? 'active' : ''; ?>">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#nocks-config-<?php echo $shop['id']; ?>"><?php echo $main_config_title; ?></a></li>
                                <li><a data-toggle="tab" href="#payment-statuses-<?php echo $shop['id']; ?>"><?php echo $status_config_title; ?></a></li>
                                <li><a data-toggle="tab" href="#about-<?php echo $shop['id']; ?>"><?php echo $about_title; ?></a></li>
                            </ul>

                            <div class="tab-content">
                                <div id="nocks-config-<?php echo $shop['id']; ?>" class="tab-pane fade in active">
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="<?php echo $code; ?>_api_key"><?php echo $entry_api_key; ?></label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-minus"></i></span>
                                                <input type="text" name="stores[<?php echo $shop['id']; ?>][<?php echo $code; ?>_api_key]" value="<?php echo $stores[$shop['id']][$code . '_api_key']; ?>" id="<?php echo $code; ?>_api_key" class="form-control" data-payment-nocks-api-key/>
                                            </div>
                                            <?php if ($stores[$shop['id']]['error_api_key']) { ?>
                                            <div class="text-danger"><?php echo $stores[$shop['id']]['error_api_key']; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="<?php echo $code; ?>_merchant"><?php echo $entry_merchant; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][<?php echo $code; ?>_merchant]" id="<?php echo $code; ?>_merchant" class="form-control"></select>
                                            <?php if ($stores[$shop['id']]['merchant']) { ?>
                                            <div class="text-danger"><?php echo $stores[$shop['id']]['merchant']; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="<?php echo $code; ?>_geo_zone"><?php echo $entry_geo_zone; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][<?php echo $code; ?>_geo_zone]" id="<?php echo $code; ?>_geo_zone" class="form-control">
                                                <option value="0"><?php echo $text_all_zones; ?></option>
                                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                                <?php if ($geo_zone['geo_zone_id'] === $stores[$shop['id']]['<?php echo $code; ?>_geo_zone']) { ?>
                                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="<?php echo $code; ?>_status"><?php echo $entry_status; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][<?php echo $code; ?>_status]" id="<?php echo $code; ?>_status" class="form-control">
                                                <?php if ($stores[$shop['id']]['<?php echo $code; ?>_status']) { ?>
                                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                                <option value="0"><?php echo $text_disabled; ?></option>
                                                <?php } else { ?>
                                                <option value="1"><?php echo $text_enabled; ?></option>
                                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="<?php echo $code; ?>_sort_order"><?php echo $entry_sort_order; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="stores[<?php echo $shop['id']; ?>][<?php echo $code; ?>_sort_order]" value="<?php echo $stores[$shop['id']][$code . '_sort_order']; ?>" placeholder="1" id="<?php echo $code; ?>_sort_order" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div id="payment-statuses-<?php echo $shop['id']; ?>" class="tab-pane fade in">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="<?php echo $code; ?>_pending_status_id"><?php echo $pending_status; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][<?php echo $code; ?>_pending_status_id]" id="<?php echo $code; ?>_pending_status_id" class="form-control">
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                <?php if ($order_status['order_status_id'] == $stores[$shop['id']][$code . '_pending_status_id']) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="<?php echo $code; ?>_failed_status_id"><?php echo $failed_status; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][<?php echo $code; ?>_failed_status_id]" id="<?php echo $code; ?>_failed_status_id" class="form-control">
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                <?php if ($order_status['order_status_id'] == $stores[$shop['id']][$code . '_failed_status_id']) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="<?php echo $code; ?>_cancelled_status_id"><?php echo $cancelled_status; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][<?php echo $code; ?>_cancelled_status_id]" id="<?php echo $code; ?>_cancelled_status_id" class="form-control">
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                <?php if ($order_status['order_status_id'] == $stores[$shop['id']][$code . '_cancelled_status_id']) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="<?php echo $code; ?>_expired_status_id"><?php echo $expired_status; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][<?php echo $code; ?>_expired_status_id]" id="<?php echo $code; ?>_expired_status_id" class="form-control">
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                <?php if ($order_status['order_status_id'] == $stores[$shop['id']][$code . '_expired_status_id']) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="<?php echo $code; ?>_completed_status_id"><?php echo $completed_status; ?></label>
                                        <div class="col-sm-10">
                                            <select name="stores[<?php echo $shop['id']; ?>][<?php echo $code; ?>_completed_status_id]" id="<?php echo $code; ?>_completed_status_id" class="form-control">
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                <?php if ($order_status['order_status_id'] == $stores[$shop['id']][$code . '_completed_status_id']) { ?>
                                                <option value="<?php $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="about-<?php echo $shop['id']; ?>" class="tab-pane fade in">
                                    <fieldset>
                                        <legend>Nocks - <a href="https://nocks.com" target="_blank">Nocks.com</a></legend>
                                        <div class="row">
                                            <label class="col-sm-2">Support</label>
                                            <div class="col-sm-10">
                                                Contact us <a href="https://www.nocks.com/support" target="_blank">here</a> or on <a href="https://github.com/nocksapp" target="_blank">Github</a>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php echo $footer; ?>

<script type="text/javascript">
    (function () {
        var timeout, xhr;
        var getMerchantsUrl = $('<div/>').html('<?php echo $get_merchants_url; ?>').text();
        var stores = JSON.parse('<?php echo json_encode($stores); ?>');

        function getMerchants(key) {
            if (xhr) xhr.abort();

            xhr = $.get(getMerchantsUrl + '&key=' + key);

            return xhr;
        }

        function validateApiKey(value, $container) {
            console.log(value);
            var iconContainer = $container.siblings('.input-group-addon');

            if (value === '') {
                updateIcon(iconContainer, 'fa-minus', null, true);
                return;
            }

            clearTimeout(timeout);
            timeout = setTimeout(function () {
                updateIcon(iconContainer, 'fa-spinner fa-spin', null);

                getMerchants(value).then(function (response) {
                    if (response.success) {
                        updateIcon(iconContainer, 'fa-check text-success');
                        // Set merchants in select
                        $tab = $container.closest('.tab-pane');
                        var shopId = $tab.attr('id').slice(-1);

                        // Remove errors
                        $tab.find('.form-group.has-error').removeClass('has-error').find('.text-danger').remove();

                        $select = $tab.find('#<?php echo $code; ?>_merchant');
                        $select.find('option').remove().end();
                        $.each(response.merchants, function (key, merchant) {
                            $select.append($('<option></option>').attr('value', merchant.value).text(merchant.label));
                        });
                        $select.val(stores[shopId]['<?php echo $code; ?>_merchant']);
                    } else {
                        updateIcon(iconContainer, 'fa-times text-danger', response.message);
                    }
                });
            }, 400);
        }

        function updateIcon($container, classes, message) {
            var icon = '<i class="fa ' + classes + '"></i>';

            $container.html(icon);
            $container.popover('destroy');

            if (message) {
                $container.popover({
                    content: '<span class="text-danger">' + message + '</span>',
                    html: true,
                    placement: 'top',
                    trigger: 'hover manual'
                });

                if ($container.is(':visible')) {
                    $container.popover('show');
                }
            }
        }

        $('[data-payment-nocks-api-key]').on('keyup', function () {
            validateApiKey(this.value, $(this));
        }).each(function () {
            validateApiKey(this.value, $(this));
        });
    })();
</script>
