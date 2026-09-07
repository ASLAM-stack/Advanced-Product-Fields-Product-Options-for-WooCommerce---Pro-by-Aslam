/**
 * Advanced Product Fields by Aslam - Frontend Reactive Engine
 *
 * Real-time dynamic pricing, formula calculations, conditional logic,
 * swatches, plus/minus steppers, AJAX file uploader, and live restaurant check summary.
 */

(function($) {
	'use strict';

	$(document).ready(function() {

		var $container = $('.apf-fields-container');
		if (!$container.length) {
			return;
		}

		var basePrice = parseFloat($container.data('base-price')) || 0.0;

		// 1. Currency Formatting Helper
		function formatPrice(amount) {
			var decimals = (typeof apfParams !== 'undefined' && apfParams.decimals !== undefined) ? parseInt(apfParams.decimals) : 2;
			var decSep   = (typeof apfParams !== 'undefined' && apfParams.decimal_sep) || '.';
			var thouSep  = (typeof apfParams !== 'undefined' && apfParams.thousand_sep) || ',';
			var symbol   = (typeof apfParams !== 'undefined' && apfParams.currency_symbol) || '$';
			var pos      = (typeof apfParams !== 'undefined' && apfParams.currency_pos) || 'left';

			var isNegative = amount < 0;
			var absAmount  = Math.abs(amount).toFixed(decimals);

			var parts = absAmount.split('.');
			parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thouSep);
			var formattedNum = parts.join(decSep);

			var result = '';
			switch (pos) {
				case 'left':
					result = symbol + formattedNum;
					break;
				case 'right':
					result = formattedNum + symbol;
					break;
				case 'left_space':
					result = symbol + ' ' + formattedNum;
					break;
				case 'right_space':
					result = formattedNum + ' ' + symbol;
					break;
				default:
					result = symbol + formattedNum;
			}

			return (isNegative ? '-' : '') + result;
		}

		// 2. Safe Mathematical Formula Evaluator
		function evaluateMathExpression(expr) {
			try {
				// Sanitize expression strictly to numbers, +, -, *, /, (, ) and decimals
				var clean = expr.replace(/[^0-9\.\+\-\*\/\(\)\s]/g, '');
				if (!clean.trim()) return 0;
				// Safe evaluation using Function constructor with no scope access
				var fn = new Function('return (' + clean + ');');
				var res = fn();
				return (!isNaN(res) && isFinite(res)) ? parseFloat(res) : 0;
			} catch (e) {
				return 0;
			}
		}

		// 3. Get Current Value of a Field by Field ID
		function getFieldValue(fieldId) {
			var $wrap = $('#apf-wrap-' + fieldId);
			if (!$wrap.length || $wrap.hasClass('apf-hidden')) {
				return null;
			}

			var type = $wrap.data('field-type');

			switch (type) {
				case 'text':
				case 'number':
				case 'date':
				case 'time':
				case 'stepper':
					return $wrap.find('input').val();
				case 'textarea':
					return $wrap.find('textarea').val();
				case 'select':
					return $wrap.find('select').val();
				case 'radio':
				case 'color_swatch':
				case 'image_swatch':
					var $checked = $wrap.find('input[type="radio"]:checked');
					return $checked.length ? $checked.val() : '';
				case 'checkbox':
					var $chks = $wrap.find('input[type="checkbox"]:checked');
					if ($chks.length > 1) {
						var vals = [];
						$chks.each(function() { vals.push($(this).val()); });
						return vals;
					} else if ($chks.length === 1) {
						return $chks.val();
					}
					return '';
				case 'file_upload':
					return $wrap.find('.apf-uploaded-file-data').val();
				default:
					return '';
			}
		}

		// 4. Conditional Logic Evaluator
		function runConditionalLogic() {
			$('.apf-field-wrap').each(function() {
				var $wrap = $(this);
				var condData = $wrap.data('conditions');

				if (!condData || condData.enabled !== 'yes' || !condData.rules || !condData.rules.length) {
					return; // No conditions on this field
				}

				var matchAll = (condData.match === 'all');
				var action   = condData.action || 'show';
				var overallMet = matchAll ? true : false;

				for (var i = 0; i < condData.rules.length; i++) {
					var rule = condData.rules[i];
					var targetVal = rule.value;
					var actualVal = getFieldValue(rule.field);
					var ruleMatched = false;

					switch (rule.operator) {
						case 'is':
							if (Array.isArray(actualVal)) {
								ruleMatched = (actualVal.indexOf(String(targetVal)) !== -1);
							} else {
								ruleMatched = (String(actualVal) === String(targetVal));
							}
							break;
						case 'is_not':
							if (Array.isArray(actualVal)) {
								ruleMatched = (actualVal.indexOf(String(targetVal)) === -1);
							} else {
								ruleMatched = (String(actualVal) !== String(targetVal));
							}
							break;
						case 'is_empty':
							ruleMatched = (actualVal === null || actualVal === '' || (Array.isArray(actualVal) && !actualVal.length));
							break;
						case 'is_not_empty':
							ruleMatched = (actualVal !== null && actualVal !== '' && (!Array.isArray(actualVal) || actualVal.length > 0));
							break;
						case 'contains':
							if (Array.isArray(actualVal)) {
								ruleMatched = (actualVal.indexOf(String(targetVal)) !== -1);
							} else {
								ruleMatched = (String(actualVal).toLowerCase().indexOf(String(targetVal).toLowerCase()) !== -1);
							}
							break;
						case 'greater_than':
							ruleMatched = (parseFloat(actualVal) > parseFloat(targetVal));
							break;
						case 'less_than':
							ruleMatched = (parseFloat(actualVal) < parseFloat(targetVal));
							break;
					}

					if (matchAll) {
						if (!ruleMatched) {
							overallMet = false;
							break;
						}
					} else {
						if (ruleMatched) {
							overallMet = true;
							break;
						}
					}
				}

				var shouldShow = (action === 'show') ? overallMet : !overallMet;

				if (shouldShow) {
					if ($wrap.hasClass('apf-hidden')) {
						$wrap.removeClass('apf-hidden').hide().slideDown(250);
					}
				} else {
					if (!$wrap.hasClass('apf-hidden')) {
						$wrap.slideUp(200, function() {
							$(this).addClass('apf-hidden');
							// Reset values when field is hidden
							resetFieldValues($wrap);
							calculateLiveTotals();
						});
					}
				}
			});
		}

		// Reset values helper for hidden fields
		function resetFieldValues($wrap) {
			var type = $wrap.data('field-type');
			if (type === 'radio' || type === 'color_swatch' || type === 'image_swatch') {
				$wrap.find('input[type="radio"]').prop('checked', false);
				$wrap.find('.selected').removeClass('selected');
			} else if (type === 'checkbox') {
				$wrap.find('input[type="checkbox"]').prop('checked', false);
				$wrap.find('.selected').removeClass('selected');
			} else if (type === 'stepper') {
				var min = parseFloat($wrap.data('min')) || 0;
				$wrap.find('.apf-stepper-input').val(min);
			} else if (type === 'select') {
				$wrap.find('select').val('');
			} else if (type === 'file_upload') {
				$wrap.find('.apf-uploaded-file-data').val('');
				$wrap.find('.apf-uploaded-preview').hide();
				$wrap.find('.apf-dropzone-box').show();
			} else {
				$wrap.find('input, textarea').val('');
			}
		}

		// 5. Dynamic Totals Calculation Engine
		function calculateLiveTotals() {
			var optionsTotal = 0.0;
			var breakdownList = [];

			$('.apf-field-wrap').not('.apf-hidden').each(function() {
				var $wrap = $(this);
				var type = $wrap.data('field-type');
				var label = $wrap.find('.apf-label-text').first().text().trim();

				// 1. Choice fields: select
				if (type === 'select') {
					$wrap.find('select option:selected').each(function() {
						var $selOpt = $(this);
						var optVal = $selOpt.val();
						if (optVal) {
							var price = parseFloat($selOpt.data('price')) || 0.0;
							var ptype = $selOpt.data('pricing-type') || 'flat';
							var delta = (ptype === 'percentage') ? (price / 100) * basePrice : price;
							optionsTotal += delta;
							if (delta !== 0) {
								var optText = $selOpt.text().split('(')[0].trim();
								breakdownList.push({ name: label + ': ' + optText, price: delta });
							}
						}
					});
				}

				// 2. Radio & Swatches
				if (type === 'radio' || type === 'color_swatch' || type === 'image_swatch') {
					var $checkedRadio = $wrap.find('input[type="radio"]:checked');
					if ($checkedRadio.length) {
						var price = parseFloat($checkedRadio.data('price')) || 0.0;
						var ptype = $checkedRadio.data('pricing-type') || 'flat';
						var delta = (ptype === 'percentage') ? (price / 100) * basePrice : price;
						optionsTotal += delta;
						var optName = $checkedRadio.data('label') || $checkedRadio.closest('label').find('.apf-card-label, .apf-swatch-title').text().trim();
						if (delta !== 0) {
							breakdownList.push({ name: label + ': ' + optName, price: delta });
						}
					}
				}

				// 3. Checkbox
				if (type === 'checkbox') {
					$wrap.find('input[type="checkbox"]:checked').each(function() {
						var $chk = $(this);
						var price = parseFloat($chk.data('price'));
						if (isNaN(price)) {
							price = parseFloat($wrap.data('pricing-amount')) || 0.0;
						}
						var ptype = $chk.data('pricing-type') || $wrap.data('pricing-type') || 'flat';
						var delta = (ptype === 'percentage') ? (price / 100) * basePrice : price;
						optionsTotal += delta;
						var chkName = $chk.closest('label').find('.apf-card-label').text().trim() || $chk.data('label') || label;
						if (delta !== 0) {
							breakdownList.push({ name: chkName, price: delta });
						}
					});
				}

				// 4. Stepper
				if (type === 'stepper') {
					var count = parseFloat($wrap.find('.apf-stepper-input').val()) || 0;
					var unitPrice = parseFloat($wrap.data('pricing-amount')) || 0.0;
					var delta = count * unitPrice;
					optionsTotal += delta;
					if (delta !== 0) {
						breakdownList.push({ name: count + 'x ' + label, price: delta });
					}
				}

				// 5. Character count pricing
				if ((type === 'text' || type === 'textarea') && $wrap.data('pricing-type') === 'char_count') {
					var textVal = $wrap.find('input, textarea').val() || '';
					var freeChars = parseInt($wrap.data('free-chars')) || 0;
					var billable = Math.max(0, textVal.length - freeChars);
					var rate = parseFloat($wrap.data('pricing-amount')) || 0.0;
					var delta = billable * rate;
					optionsTotal += delta;
					if (delta !== 0) {
						breakdownList.push({ name: label + ' (' + billable + ' billable chars)', price: delta });
					}
				}

				// 6. Custom Formula
				if ($wrap.data('pricing-type') === 'formula') {
					var formula = $wrap.data('formula') || '';
					if (formula) {
						var expr = formula.replace(/\[price\]/gi, basePrice);
						$('.apf-field-wrap').each(function() {
							var fid = $(this).data('field-id');
							var val = parseFloat(getFieldValue(fid)) || 0;
							expr = expr.replace(new RegExp('\\[' + fid + '\\]', 'gi'), val);
						});
						var delta = evaluateMathExpression(expr);
						optionsTotal += delta;
						if (delta !== 0) {
							breakdownList.push({ name: label, price: delta });
						}
					}
				}

				// 7. Generic flat / percentage fee on input/number
				var genericType = $wrap.data('pricing-type');
				if (genericType === 'flat' || genericType === 'percentage') {
					var inputVal = $wrap.find('input').val();
					if (inputVal && inputVal.trim()) {
						var pAmt = parseFloat($wrap.data('pricing-amount')) || 0;
						var delta = (genericType === 'percentage') ? (pAmt / 100) * basePrice : pAmt;
						optionsTotal += delta;
						if (delta !== 0) {
							breakdownList.push({ name: label, price: delta });
						}
					}
				}
			});

			var grandTotal = Math.max(0, basePrice + optionsTotal);

			// Render Live Order Summary
			$('#apf-summary-base-price').text(formatPrice(basePrice));
			$('#apf-summary-addons-total').text(formatPrice(optionsTotal));

			var $grand = $('#apf-summary-grand-total');
			$grand.text(formatPrice(grandTotal));
			$grand.addClass('apf-pulse');
			setTimeout(function() { $grand.removeClass('apf-pulse'); }, 250);

			// Render Options breakdown rows
			var $list = $('#apf-summary-options-list');
			$list.empty();
			if (breakdownList.length) {
				breakdownList.forEach(function(item) {
					var $row = $('<div class="apf-summary-option-line"></div>');
					var $optName = $('<span class="opt-name"></span>').text(item.name);
					var $optCost = $('<span class="opt-cost"></span>').text((item.price > 0 ? '+' : '') + formatPrice(item.price));
					$row.append($optName).append($optCost);
					$list.append($row);
				});
				$list.show();
			} else {
				$list.hide();
			}

			// Update WooCommerce core single product price header
			var $wcPrice = $('.summary .price .woocommerce-Price-amount, .woocommerce-variation-price .price .woocommerce-Price-amount').last();
			if ($wcPrice.length) {
				$wcPrice.html(formatPrice(grandTotal));
			}
		}

		// 6. Stepper Interactions
		$(document).on('click', '.apf-step-up', function(e) {
			e.preventDefault();
			var $wrap = $(this).closest('.apf-type-stepper');
			var $input = $wrap.find('.apf-stepper-input');
			var max = parseFloat($wrap.data('max'));
			var step = parseFloat($wrap.data('step')) || 1;
			var current = parseFloat($input.val()) || 0;

			if (isNaN(max) || current + step <= max) {
				$input.val(current + step);
				runConditionalLogic();
				calculateLiveTotals();
			}
		});

		$(document).on('click', '.apf-step-down', function(e) {
			e.preventDefault();
			var $wrap = $(this).closest('.apf-type-stepper');
			var $input = $wrap.find('.apf-stepper-input');
			var min = parseFloat($wrap.data('min')) || 0;
			var step = parseFloat($wrap.data('step')) || 1;
			var current = parseFloat($input.val()) || 0;

			if (current - step >= min) {
				$input.val(current - step);
				runConditionalLogic();
				calculateLiveTotals();
			}
		});

		// 7. Swatches & Choice Card Selection Handlers (Native change event prevents double-toggle)
		$(document).on('change', '.apf-choice-card input, .apf-swatch-item input', function() {
			var $input = $(this);
			var $card = $input.closest('.apf-choice-card, .apf-swatch-item');

			if ($input.is(':radio')) {
				var groupName = $input.attr('name');
				if (groupName) {
					$('input[name="' + groupName + '"]').closest('.apf-choice-card, .apf-swatch-item').removeClass('selected');
				}
				if ($input.is(':checked')) {
					$card.addClass('selected');
					var swatchName = $input.data('label') || $card.find('.apf-swatch-title, .apf-card-label').text().trim();
					$card.closest('.apf-field-wrap').find('.apf-selected-swatch-name').text('— ' + swatchName);
				}
			} else if ($input.is(':checkbox')) {
				$card.toggleClass('selected', $input.is(':checked'));
			}

			runConditionalLogic();
			calculateLiveTotals();
		});

		// Fallback for click if card is not a label element
		$(document).on('click', '.apf-choice-card:not(label), .apf-swatch-item:not(label)', function(e) {
			if ($(e.target).is('input, label, a, button')) return;
			var $inp = $(this).find('input');
			if ($inp.is(':checkbox')) {
				$inp.prop('checked', !$inp.prop('checked')).trigger('change');
			} else if ($inp.is(':radio')) {
				$inp.prop('checked', true).trigger('change');
			}
		});

		// 8. Text Input / Textarea Character Counting & Updates
		$(document).on('input keyup change', '.apf-input', function() {
			var $wrap = $(this).closest('.apf-field-wrap');
			var val = $(this).val() || '';
			$wrap.find('.apf-char-count').text(val.length);

			runConditionalLogic();
			calculateLiveTotals();
		});

		// 9. Dropdown Select Changes
		$(document).on('change', '.apf-select', function() {
			runConditionalLogic();
			calculateLiveTotals();
		});

		// 10. AJAX Drag-and-Drop File Uploader
		$('.apf-dropzone-wrap').each(function() {
			var $wrap     = $(this);
			var $box      = $wrap.find('.apf-dropzone-box');
			var $fileInp  = $wrap.find('.apf-file-input');
			var $dataVal  = $wrap.find('.apf-uploaded-file-data');
			var $preview  = $wrap.find('.apf-uploaded-preview');
			var $progress = $wrap.find('.apf-upload-progress');
			var $bar      = $wrap.find('.apf-progress-fill');
			var fieldId   = $wrap.data('field-id');
			var productId = $wrap.closest('.apf-fields-container').data('product-id') || $container.data('product-id') || 0;
			var defaultThumb = $preview.find('.apf-preview-thumb').html();

			$box.on('click', function() {
				$fileInp.trigger('click');
			});

			$box.on('dragover dragenter', function(e) {
				e.preventDefault();
				e.stopPropagation();
				$box.addClass('dragover');
			});

			$box.on('dragleave dragend drop', function(e) {
				e.preventDefault();
				e.stopPropagation();
				$box.removeClass('dragover');
			});

			$box.on('drop', function(e) {
				var files = e.originalEvent.dataTransfer.files;
				if (files.length) {
					uploadFile(files[0]);
				}
			});

			$fileInp.on('change', function() {
				if (this.files.length) {
					uploadFile(this.files[0]);
				}
			});

			function uploadFile(file) {
				var formData = new FormData();
				formData.append('action', 'apf_upload_file');
				formData.append('nonce', apfParams.nonce);
				formData.append('field_id', fieldId);
				formData.append('product_id', productId);
				formData.append('apf_file', file);

				$box.find('.apf-dropzone-content').hide();
				$progress.show();
				$bar.css('width', '10%');

				$.ajax({
					url: apfParams.ajax_url,
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					xhr: function() {
						var xhr = new window.XMLHttpRequest();
						xhr.upload.addEventListener('progress', function(evt) {
							if (evt.lengthComputable) {
								var percent = Math.round((evt.loaded / evt.total) * 100);
								$bar.css('width', percent + '%');
							}
						}, false);
						return xhr;
					},
					success: function(resp) {
						if (resp.success) {
							var fileData = resp.data;
							$dataVal.val(JSON.stringify({ url: fileData.url, name: fileData.name }));
							$box.hide();
							$progress.hide();

							$preview.find('.apf-filename').text(fileData.name);
							$preview.find('.apf-filesize').text(fileData.size);
							if (fileData.is_image) {
								$preview.find('.apf-preview-thumb').html('<img src="' + fileData.url + '" style="width:36px;height:36px;object-fit:cover;border-radius:50px;">');
							}
							$preview.show();
							calculateLiveTotals();
						} else {
							alert(resp.data && resp.data.message ? resp.data.message : apfParams.i18n.upload_error);
							resetUploader();
						}
					},
					error: function() {
						alert(apfParams.i18n.upload_error);
						resetUploader();
					}
				});
			}

			function resetUploader() {
				$fileInp.val('');
				$dataVal.val('');
				$progress.hide();
				$bar.css('width', '0%');
				$preview.find('.apf-preview-thumb').html(defaultThumb);
				$box.find('.apf-dropzone-content').show();
				$box.show();
				$preview.hide();
			}

			$wrap.on('click', '.apf-btn-remove-uploaded', function(e) {
				e.preventDefault();
				var fileInfo = $dataVal.val();
				if (fileInfo) {
					try {
						var parsed = JSON.parse(fileInfo);
						$.post(apfParams.ajax_url, {
							action: 'apf_remove_file',
							nonce: apfParams.nonce,
							file_url: parsed.url
						});
					} catch (err) {}
				}
				resetUploader();
				calculateLiveTotals();
			});
		});

		// 11. Initial execution on page load
		$('.apf-choice-card input:checked, .apf-swatch-item input:checked').each(function() {
			var $inp = $(this);
			var $card = $inp.closest('.apf-choice-card, .apf-swatch-item');
			$card.addClass('selected');
			if ($inp.is(':radio')) {
				var swatchName = $inp.data('label') || $card.find('.apf-swatch-title, .apf-card-label').text().trim();
				if (swatchName) {
					$card.closest('.apf-field-wrap').find('.apf-selected-swatch-name').text('— ' + swatchName);
				}
			}
		});
		runConditionalLogic();
		calculateLiveTotals();

		// Hook into WooCommerce variable product changes
		$('form.variations_form').on('found_variation', function(e, variation) {
			if (variation && variation.display_price !== undefined) {
				basePrice = parseFloat(variation.display_price);
				$container.attr('data-base-price', basePrice);
				calculateLiveTotals();
			}
		});

		// Hook into WooCommerce variable product reset
		$('form.variations_form').on('reset_data', function() {
			basePrice = parseFloat($container.data('base-price')) || 0.0;
			calculateLiveTotals();
		});

		// Hook into quick-view popups & AJAX fragments (Barab woo-smart-quick-view, etc.)
		$(document).on('wc_fragments_refreshed quick_view_loaded woosq_loaded woosq_popup_opened', function() {
			$container = $('.apf-fields-container');
			if ($container.length) {
				basePrice = parseFloat($container.data('base-price')) || 0.0;
				runConditionalLogic();
				calculateLiveTotals();
			}
		});

	});

})(jQuery);
