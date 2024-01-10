<div class="modal micromodal-slide wcs-sub-box-frequency-modal" id="wcs_sub_box_frequency_modal"
     data-subscription_id="<?php echo $subscription_id; ?>" data-item_id="<?php echo $item_id; ?>" data-item_id="<?php echo $found_item_id; ?>" data-old_variation_name="<?php echo $attributes[$subscription_selected_variation_id]; ?>" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container" role="dialog" aria-modal="true"
             aria-labelledby="wcs_sub_box_frequency_modal_title">
            <div class="wcs_sub_box_frequency_modal-container">
                <header class="modal__header header-sticky">
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                    <div class="modal__title">
                        <h4><?php _e('Change Frequency Subscription', 'zain-installments'); ?></h4>
                    </div>
                </header>
                <main class="modal__content">
                    <div class="wcs-check-frequency-modal modal_body">
                        <label for="variations"><?php _e('References','wc-sub-frequency');?>:</label>
                        <select name="variations" id="variation">
                            <?php foreach ($attributes as $key => $value) { ?>
                                <option class=""
                                        value="<?php echo $key; ?>" <?php echo($subscription_selected_variation_id == $key ? 'selected' : ''); ?>>
                                    <?php echo $value; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </main>
                <footer class="wcs-check-frequency-modal modal__footer">
                    <button type="button" class="modal__btn modal__close wp-element-button"
                            data-dismiss="modal">
                        <?php _e('Close','wc-sub-frequency');?>
                    </button>
                    <button type="button" id="wcs-sub-box-frequency-continue-button" class="modal__btn wp-element-button"><?php _e('Continue','wc-sub-frequency');?>
                    </button>
                </footer>
            </div>
        </div>
    </div>

<?php
