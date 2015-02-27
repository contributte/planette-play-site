$(function () {

    $.nette.init();

    // dirty prototype version
    var autofocus = $('[data-autoselect]');
    if (autofocus[0]){
        autofocus[0].focus();
    }

    var homepage = $('#search-component');
    if (homepage[0] && KnowledgeBaseConfig) {



        var search, interval;
        var slider = $(".top-slider");
        var selectedItem = 0;

        var loader = function(state) {
			if (state == 'show') {
				$(".search-results__loader").show();
				$(".search-results__loader-placeholder").hide();
			}
			if (state == 'hide') {
				$(".search-results__loader").hide();
				$(".search-results__loader-placeholder").show();
			}
		}

		loader('hide');


        var selectSearchResultItem = function (itemNum, $searchResult) {
            $searchResult.removeClass('search-results__item--active');
            var selectedItemObject = $($searchResult[(itemNum - 1)]);
			selectedItemObject.addClass('search-results__item--active');
			scrollPageToSelectedItem(selectedItemObject);
        };

        var unselectSearchResultItem = function ($searchResult) {

            $searchResult.removeClass('search-results__item--active');
        };

		var scrollPageToSelectedItem = function (selectedItemObject) {
			var itemTopPosition = selectedItemObject.offset().top;
			var windowHeight = $(window).height();
			var offset = 200;
			if (itemTopPosition > windowHeight - offset) {
				scrollTo(itemTopPosition - windowHeight + offset);
			} else {
				scrollTo(0);
			}
		};

		var scrollTo = function(position) {
			$('html, body').stop().animate({
				scrollTop: position
			}, 100);
		}


		$("body").on("keydown", function (e) {
			var searchResult = $('.search-results__item');
			var searchInput = $('.homepage-search-input');

			if (e.keyCode == 38 || e.keyCode == 40 || e.keyCode == 13) {
				e.stopImmediatePropagation();
				e.preventDefault();
			}

			if (e.keyCode == 38) {
				// arrow up
				if (searchResult) {
					selectedItem--;
					selectedItem = Math.max(0, selectedItem);
					if (selectedItem === 0) {
						// focus back to input
						searchInput.trigger('focus');
						unselectSearchResultItem(searchResult);
					} else {
						selectSearchResultItem(selectedItem, searchResult);
					}

				}

			} else if (e.keyCode == 40) {
				// arrow down
				if (searchResult) {
					selectedItem++;
					if (selectedItem == 1) {
						// blur the search input
						searchInput.trigger('blur');
					}

					selectedItem = Math.min(searchResult.length, selectedItem);
					selectSearchResultItem(selectedItem, searchResult);
				}

			} else if (e.keyCode == 13) {
				// enter
				// TODO refactor
				if (selectedItem > 0) {
					var href = $($(searchResult[(selectedItem - 1)]).find('a')[0]).attr('href');
					window.location.href = href;
				}
			}
		});


		$(".homepage-search-input").on("keyup", function (e) {

			if (e.keyCode != 38 && e.keyCode != 40 && e.keyCode != 13) {
				// only letter typing
				var input = $(this);
				loader('show');
				clearTimeout(interval);
				interval = setTimeout(function () {
					search = $.nette.ajax({
						url: KnowledgeBaseConfig.searchUrl,
						data: {'search-q': input.val()}
					});
					history.pushState(null, null, KnowledgeBaseConfig.searchUrl + "&search-q=" + encodeURIComponent(input.val()));
					loader('hide');
				}, 500);

				if (input.val() != '') {
					slider.removeClass("position-default").addClass("position-top");
				} else {
					slider.removeClass("position-top").addClass("position-default");
				}
			}

        });
    }


    var codeEditor = $('#code');
    if (codeEditor[0]) {

        var map = {
            "Cmd-B": function (cm) {
                var selection = cm.doc.getSelection();
                if (selection) {
                    cm.doc.replaceSelection('**' + selection + '**');
                } else {
                    var cur = cm.doc.getCursor();
                    cm.doc.replaceRange('****', cur);
                    cm.doc.setCursor(cur.line, cur.ch + 2);
                }
            },
            "Cmd-I": function (cm) {
                var selection = cm.doc.getSelection();
                if (selection) {
                    cm.doc.replaceSelection('*' + selection + '*');
                } else {
                    var cur = cm.doc.getCursor();
                    cm.doc.replaceRange('**', cur);
                    cm.doc.setCursor(cur.line, cur.ch + 1);
                }
            },
            "Cmd-S": function (cm) {
                // todo... ajax save
            }
        };

        var editor = CodeMirror.fromTextArea(codeEditor[0], {
            theme: "elegant"
            //, viewportMargin: "Infinity"
        });

        // TODO: use DI in JavaScript
        // saveImagePath is defined in html
        if (saveImagePath !== undefined){
            var options = {
                uploadUrl: saveImagePath,
                urlText: "[* {filename} *]"
            }

            inlineAttachment.editors.codemirror4.attach(editor, options);
        }

        editor.addKeyMap(map);

        $("#tags").select2({
            tags: ($('#tags').data('tags-default')).split(';'),
            tokenSeparators: [",", ";"]
        });
    }

});