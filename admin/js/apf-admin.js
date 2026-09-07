/**
 * Advanced Product Fields by Aslam - Admin Field Builder Scripts
 */

(function($) {
	'use strict';

	$(document).ready(function() {

		// 1. Initialize Color Pickers
		function initColorPickers($context) {
			$context = $context || $(document);
			if ($.fn.wpColorPicker) {
				$context.find('.apf-color-picker, .apf-color-picker-input').not('.wp-color-picker').wpColorPicker();
			}
		}
		initColorPickers();

		// 2. Sortable Fields Builder
		if ($.fn.sortable) {
			$('#apf-fields-sortable').sortable({
				handle: '.apf-drag-handle',
				items: '.apf-field-card',
				opacity: 0.7,
				axis: 'y',
				cursor: 'grabbing',
				update: function(e, ui) {
					updateConditionalFieldChoices();
				}
			});

			$('.apf-options-tbody').sortable({
				handle: '.apf-opt-handle',
				items: '.apf-option-row',
				opacity: 0.7,
				axis: 'y',
				cursor: 'grabbing'
			});
		}

		// 3. Tab Switching within Field Cards
		$(document).on('click', '.apf-card-tabs li', function() {
			var $tab = $(this);
			var target = $tab.data('tab');
			var $card = $tab.closest('.apf-field-card');

			$card.find('.apf-card-tabs li').removeClass('active');
			$tab.addClass('active');

			$card.find('.apf-tab-pane').removeClass('active');
			$card.find('.apf-tab-pane[data-pane="' + target + '"]').addClass('active');
		});

		// 4. Toggle Card Expand / Collapse
		$(document).on('click', '.apf-field-card-header', function(e) {
			if ($(e.target).closest('.apf-header-right, .apf-drag-handle').length) {
				return;
			}
			var $card = $(this).closest('.apf-field-card');
			$card.toggleClass('open');
		});

		$(document).on('click', '.apf-btn-toggle', function() {
			var $card = $(this).closest('.apf-field-card');
			$card.toggleClass('open');
		});

		// 5. Live Field Title Synchronization
		$(document).on('keyup change', '.apf-field-label-input', function() {
			var val = $(this).val().trim();
			var $card = $(this).closest('.apf-field-card');
			$card.find('.apf-field-title-preview').text(val || '(Untitled Field)');
			updateConditionalFieldChoices();
		});

		// 6. Field Type Change Handler
		$(document).on('change', '.apf-field-type-select', function() {
			var $select = $(this);
			var type = $select.val();
			var $card = $select.closest('.apf-field-card');
			var $optTab = $card.find('.tab-options-link');
			var $prodTab = $card.find('.tab-products-link');
			var typeText = $select.find('option:selected').text().trim();

			// Update badge text
			$card.find('.apf-type-badge-text').text(typeText);

			var choiceTypes = ['select', 'radio', 'checkbox', 'color_swatch', 'image_swatch'];
			if (choiceTypes.indexOf(type) !== -1) {
				$optTab.show();
			} else {
				$optTab.hide();
				if ($optTab.hasClass('active')) {
					$card.find('.apf-card-tabs li[data-tab="general"]').trigger('click');
				}
			}

			// Show/hide recommended products tab
			var isProducts = (type === 'products' || type === 'recommended_products');
			if (isProducts) {
				$prodTab.show();
			} else {
				$prodTab.hide();
				if ($prodTab.hasClass('active')) {
					$card.find('.apf-card-tabs li[data-tab="general"]').trigger('click');
				}
			}

			// Show/hide swatch visual column in options table
			var isSwatch = (type === 'color_swatch' || type === 'image_swatch');
			$card.find('.col-option-visual').toggle(isSwatch);
			$card.find('.visual-color-wrap').toggle(type === 'color_swatch');
			$card.find('.visual-image-wrap').toggle(type === 'image_swatch');
		});

		// 6b. Recommended Products Pricing Mode Toggle
		$(document).on('change', '.apf-rec-pricing-mode', function() {
			var mode = $(this).val();
			var $wrap = $(this).closest('.apf-tab-pane').find('.apf-rec-pricing-amount-group');
			$wrap.toggle(mode === 'discount_pct' || mode === 'flat');
		});

		// 7. Pricing Type Change Handler
		$(document).on('change', '.apf-pricing-type-select', function() {
			var val = $(this).val();
			var $card = $(this).closest('.apf-field-card');

			$card.find('.apf-pricing-amount-wrap').toggle(val !== 'none');
			$card.find('.apf-char-pricing-wrap').toggle(val === 'char_count');
			$card.find('.apf-formula-pricing-wrap').toggle(val === 'formula');
		});

		// 8. Conditional Logic Toggle
		$(document).on('change', '.apf-cond-enable-toggle', function() {
			var isChecked = $(this).is(':checked');
			var $card = $(this).closest('.apf-field-card');
			$card.find('.apf-conditions-rules-wrap').toggle(isChecked);
		});

		// 9. Add New Field
		$(document).on('click', '.apf-btn-add-field', function() {
			var tmpl = $('#tmpl-apf-field-card').html();
			var uniqueId = 'field_' + Math.random().toString(36).substring(2, 10);
			var html = tmpl.replace(/\{\{field_id\}\}/g, uniqueId);
			var $newCard = $(html);

			$('#apf-fields-sortable').append($newCard);
			$('.apf-empty-state').hide();

			$newCard.addClass('open');
			initColorPickers($newCard);

			if ($.fn.sortable) {
				$newCard.find('.apf-options-tbody').sortable({
					handle: '.apf-opt-handle',
					items: '.apf-option-row',
					axis: 'y'
				});
			}

			$('html, body').animate({
				scrollTop: $newCard.offset().top - 60
			}, 300);

			updateConditionalFieldChoices();
		});

		// 10. Delete Field
		$(document).on('click', '.apf-btn-delete', function() {
			var confirmMsg = (window.apfAdminParams && apfAdminParams.i18n.delete_confirm) || 'Delete this field?';
			if (confirm(confirmMsg)) {
				var $card = $(this).closest('.apf-field-card');
				$card.slideUp(250, function() {
					$(this).remove();
					if ($('#apf-fields-sortable .apf-field-card').length === 0) {
						$('.apf-empty-state').show();
					}
					updateConditionalFieldChoices();
				});
			}
		});

		// 11. Duplicate Field
		$(document).on('click', '.apf-btn-duplicate', function() {
			var $card = $(this).closest('.apf-field-card');
			var $clone = $card.clone();
			var newId = 'field_' + Math.random().toString(36).substring(2, 10);
			var oldId = $card.data('field-id');

			$clone.attr('data-field-id', newId);
			$clone.find('.apf-field-id-input').val(newId);

			// Replace all occurrences of old ID in form name attributes
			$clone.find('[name*="' + oldId + '"]').each(function() {
				var name = $(this).attr('name');
				$(this).attr('name', name.replace(oldId, newId));
			});

			$clone.find('.apf-field-label-input').val($clone.find('.apf-field-label-input').val() + ' (Copy)');
			$clone.find('.apf-field-title-preview').text($clone.find('.apf-field-title-preview').text() + ' (Copy)');

			// Clear color picker wrappers before re-initializing
			$clone.find('.wp-picker-container').replaceWith(function() {
				return $('.apf-color-picker-input', this);
			});

			$card.after($clone);
			initColorPickers($clone);
			updateConditionalFieldChoices();
		});

		// 12. Add New Option Row
		$(document).on('click', '.apf-btn-add-option', function() {
			var $tbody = $(this).closest('.apf-tab-pane').find('.apf-options-tbody');
			var $card = $(this).closest('.apf-field-card');
			var fieldId = $card.data('field-id');
			var optIdx = $tbody.find('.apf-option-row').length;
			var fieldType = $card.find('.apf-field-type-select').val();
			var isSwatch = (fieldType === 'color_swatch' || fieldType === 'image_swatch');

			var rowHtml = '<tr class="apf-option-row">' +
				'<td class="apf-opt-handle"><i class="fa-solid fa-grip-lines"></i></td>' +
				'<td class="col-option-visual"' + (isSwatch ? '' : ' style="display:none;"') + '>' +
					'<div class="visual-color-wrap"' + (fieldType === 'color_swatch' ? '' : ' style="display:none;"') + '>' +
						'<input type="text" name="apf_fields[' + fieldId + '][options][' + optIdx + '][color]" value="#EB1400" class="apf-color-picker-input">' +
					'</div>' +
					'<div class="visual-image-wrap"' + (fieldType === 'image_swatch' ? '' : ' style="display:none;"') + '>' +
						'<div class="apf-image-preview-box"><i class="fa-regular fa-image"></i></div>' +
						'<input type="hidden" name="apf_fields[' + fieldId + '][options][' + optIdx + '][image_url]" value="" class="apf-opt-image-url">' +
						'<input type="hidden" name="apf_fields[' + fieldId + '][options][' + optIdx + '][image_id]" value="0" class="apf-opt-image-id">' +
						'<button type="button" class="button button-small apf-btn-upload-image">Select</button>' +
					'</div>' +
				'</td>' +
				'<td><input type="text" name="apf_fields[' + fieldId + '][options][' + optIdx + '][label]" value="" placeholder="Option Label" class="widefat apf-opt-label"></td>' +
				'<td><input type="text" name="apf_fields[' + fieldId + '][options][' + optIdx + '][value]" value="" placeholder="option_val" class="widefat apf-opt-val"></td>' +
				'<td>' +
					'<select name="apf_fields[' + fieldId + '][options][' + optIdx + '][pricing_type]" class="widefat">' +
						'<option value="flat">Flat Fee (+/-)</option>' +
						'<option value="quantity">Per Item Qty (+/-)</option>' +
						'<option value="percentage">Percentage (%)</option>' +
					'</select>' +
				'</td>' +
				'<td><input type="number" step="0.01" name="apf_fields[' + fieldId + '][options][' + optIdx + '][pricing_amount]" value="0" class="widefat"></td>' +
				'<td><button type="button" class="apf-btn-icon apf-btn-remove-option"><i class="fa-solid fa-xmark"></i></button></td>' +
			'</tr>';

			var $row = $(rowHtml);
			$tbody.append($row);
			initColorPickers($row);
		});

		// 13. Auto-populate Option Value slug from Option Label
		$(document).on('keyup', '.apf-opt-label', function() {
			var $valInput = $(this).closest('tr').find('.apf-opt-val');
			if (!$valInput.val() || $valInput.data('auto') !== false) {
				var slug = $(this).val().toLowerCase().replace(/[^a-z0-9]/g, '_').replace(/_+/g, '_').replace(/^_+|_+$/g, '');
				$valInput.val(slug);
				$valInput.data('auto', true);
			}
		});

		$(document).on('input', '.apf-opt-val', function() {
			$(this).data('auto', false);
		});

		// 14. Remove Option Row
		$(document).on('click', '.apf-btn-remove-option', function() {
			var $tbody = $(this).closest('.apf-options-tbody');
			if ($tbody.find('.apf-option-row').length > 1) {
				$(this).closest('.apf-option-row').remove();
			}
		});

		// 15. Media Uploader for Image Swatches
		var mediaFrame;
		$(document).on('click', '.apf-btn-upload-image', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var $wrap = $btn.closest('.visual-image-wrap');

			mediaFrame = wp.media({
				title: (window.apfAdminParams && apfAdminParams.i18n.choose_image) || 'Choose Swatch Image',
				button: {
					text: (window.apfAdminParams && apfAdminParams.i18n.use_image) || 'Use Image'
				},
				multiple: false
			});

			mediaFrame.on('select', function() {
				var attachment = mediaFrame.state().get('selection').first().toJSON();
				$wrap.find('.apf-opt-image-url').val(attachment.url);
				$wrap.find('.apf-opt-image-id').val(attachment.id);
				$wrap.find('.apf-image-preview-box').html('<img src="' + attachment.url + '" alt="Swatch">');
			});

			mediaFrame.open();
		});

		// 16. Conditional Logic: Add Rule
		$(document).on('click', '.apf-btn-add-rule', function() {
			var $rulesList = $(this).closest('.apf-conditions-rules-wrap').find('.apf-cond-rules-list');
			var $card = $(this).closest('.apf-field-card');
			var fieldId = $card.data('field-id');
			var ruleIdx = $rulesList.find('.apf-cond-rule-row').length;

			var ruleHtml = '<div class="apf-cond-rule-row">' +
				'<select name="apf_fields[' + fieldId + '][conditional_logic][rules][' + ruleIdx + '][field]" class="apf-rule-field-select">' +
					'<option value="">-- Select Field --</option>' +
				'</select>' +
				'<select name="apf_fields[' + fieldId + '][conditional_logic][rules][' + ruleIdx + '][operator]">' +
					'<option value="is">is equal to</option>' +
					'<option value="is_not">is not equal to</option>' +
					'<option value="contains">contains</option>' +
					'<option value="greater_than">is greater than</option>' +
					'<option value="less_than">is less than</option>' +
					'<option value="is_empty">is empty</option>' +
					'<option value="is_not_empty">is not empty</option>' +
				'</select>' +
				'<input type="text" name="apf_fields[' + fieldId + '][conditional_logic][rules][' + ruleIdx + '][value]" value="" placeholder="Value to match...">' +
				'<button type="button" class="apf-btn-icon apf-btn-remove-rule" title="Delete Rule">' +
					'<i class="fa-solid fa-trash-can"></i>' +
				'</button>' +
			'</div>';

			var $rule = $(ruleHtml);
			$rulesList.append($rule);
			populateFieldsForSelect($rule.find('.apf-rule-field-select'), fieldId);
		});

		// 17. Conditional Logic: Remove Rule
		$(document).on('click', '.apf-btn-remove-rule', function() {
			$(this).closest('.apf-cond-rule-row').remove();
		});

		// 18. Helper: Update conditional choices dropdown for all fields
		function updateConditionalFieldChoices() {
			$('.apf-field-card').each(function() {
				var currentFieldId = $(this).data('field-id');
				$(this).find('.apf-rule-field-select').each(function() {
					var $select = $(this);
					var currentVal = $select.val();
					populateFieldsForSelect($select, currentFieldId, currentVal);
				});
			});
		}

		function escapeHtml(str) {
			return $('<div>').text(str || '').html();
		}

		function populateFieldsForSelect($select, excludeFieldId, selectedVal) {
			var optionsHtml = '<option value="">-- Select Field --</option>';
			$('.apf-field-card').each(function() {
				var fid = $(this).data('field-id');
				if (fid !== excludeFieldId) {
					var rawLabel = $(this).find('.apf-field-label-input').val();
					var label = (rawLabel && rawLabel.trim()) ? rawLabel.trim() : fid;
					var isSel = (fid === selectedVal) ? ' selected' : '';
					optionsHtml += '<option value="' + escapeHtml(fid) + '"' + isSel + '>' + escapeHtml(label) + '</option>';
				}
			});
			$select.html(optionsHtml);
		}

		// Initial population of conditional choices
		setTimeout(updateConditionalFieldChoices, 200);

		// 19. Display Rules CPT Metabox Toggle (All vs Specific Products vs Categories)
		$('input[name="apf_rules[apply_to]"]').on('change', function() {
			var val = $(this).val();
			$('.apf-rule-categories-box').toggle(val === 'categories');
			$('.apf-rule-products-box').toggle(val === 'specific_products');
		});

	});

})(jQuery);
