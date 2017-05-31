//https://code.google.com/p/smartgoal/source/browse/jquery.combobox.js?r=8

(function () {

    jQuery.fn.combobox = function (selectOptions) {
        return this.each(function () {
            var newCombobox = new Combobox(this, selectOptions);
            jQuery.combobox.instances.push(newCombobox);

        });
    };

    jQuery.combobox = {
        instances : []
    };

    var Combobox = function (textInputElement, selectOptions) {

		//custom by vu.nguyen
		//console.log(textInputElement.id);
		//if(textInputElement.id=='InvoiceCountry' || textInputElement.id=='ShippingCountry ')
			//selectOptions = this.SortPluginKey(selectOptions,'@');
		//end custom

        this.textInputElement = jQuery(textInputElement);
        this.textInputElement.wrap(
            '<span class="combobox" style="position:relative; '+
            'display:-moz-inline-box; display:inline-block;"/>'
        );
        this.selector = new ComboboxSelector(this);
        this.setSelectOptions(selectOptions);
        var inputHeight = this.textInputElement.outerHeight();
        var buttonLeftPosition = this.textInputElement.outerWidth() + 0;
        var showSelectorButton = jQuery(
            '<span class="combobox_button" '+
            'style="cursor:pointer;position:absolute; height:'+inputHeight+'px; width:'+
            // BaoNam
            // inputHeight+'px; top:0; left:'+buttonLeftPosition+'px;"><div class="combobox_arrow"></div></a>'
            inputHeight+'px; top:0; right: -12px;;"><div class="combobox_arrow"></div></span>'
        );
        this.textInputElement.css('margin', '0 '+showSelectorButton.outerWidth()+'px 0 0');
        showSelectorButton.insertAfter(this.textInputElement);
        var thisSelector = this.selector;
        var thisCombobox = this;
        showSelectorButton.click(function (e) {
            jQuery('html').trigger('click');
            thisSelector.buildSelectOptionList();
            thisSelector.show();
            thisCombobox.focus();
            return false;
        });
		this.textInputElement.click(function (e) {
			$(".combobox_selector").css("display","none");
            thisSelector.buildSelectOptionList();
			thisSelector.show();
            return false;
        });
		this.textInputElement.focus(function (e) {
            $(".combobox_selector").css("display","none");
            thisSelector.buildSelectOptionList();
			thisSelector.show();
            return false;
        });
        this.bindKeypress();
    };

    Combobox.prototype = {

        setSelectOptions : function (selectOptions) {
            this.selector.setSelectOptions(selectOptions);
			this.selector.checkblank(this.textInputElement[0].id);//vu.nguyen add
            this.selector.buildSelectOptionList(this.getValue());
        },

        bindKeypress : function () {
            var thisCombobox = this;
            this.textInputElement.keyup(function (event) {
                if (event.keyCode == Combobox.keys.TAB
                    || event.keyCode == Combobox.keys.SHIFT)
                {
                    return;
                }
                if (event.keyCode != Combobox.keys.DOWNARROW
                    && event.keyCode != Combobox.keys.UPARROW
                    && event.keyCode != Combobox.keys.ESCAPE
                    && event.keyCode != Combobox.keys.ENTER)
                {
					//vu.nguyen add
					if($("#" + thisCombobox.textInputElement[0].id).prop('readonly')){
						var kn = event.keyCode;
						var chr = String.fromCharCode(kn);
						thisCombobox.selector.buildSelectOptionList(chr);
					}else
					//end add
                    	thisCombobox.selector.buildSelectOptionList(thisCombobox.getValue());
                }
                if (event.keyCode === Combobox.keys.ENTER)
                {
                    return;
                }
                thisCombobox.selector.show();
            });
        },

        setValue : function (value, key) {
            var oldValue = this.textInputElement.val();
            this.textInputElement.val(value);
			var oldId = $("#" + this.textInputElement[0].id + "Id").val();
            // BaoNam 10/09/2013
            $("#" + this.textInputElement[0].id + "Id").val(key);
            // end

            if (oldValue != value) {
                this.textInputElement.trigger('change');
            }
        },

        getValue : function () {
            return this.textInputElement.val();
        },

        focus : function () {
            this.textInputElement.trigger('focus');
        },
		//custom by vu.nguyen
		/**
		* Sort Object and add string more
		* @using arr{0:'bbb',1:'aaa'} => arr{aaa@1:'aaa',bbb@0:'bbb'}
		* @version 1.0
		*/
		SortPluginKey : function (arr,keys) {
            var obj = new Object();
			var ret = new Object();
			var arrtmp = new Array();
			var temp;
			for(var i in arr) {
				temp = arr[i];
				obj[temp] = temp+keys+i;
				arrtmp.push(temp);
			}
			arrtmp.sort();
			for(var i in arrtmp) {
				temp = arrtmp[i];
				temp = obj[temp];
				ret[temp] = arrtmp[i];
			}
			return ret;
        },
		//end add

    };

    Combobox.keys = {
        UPARROW : 38,
        DOWNARROW : 40,
        ENTER : 13,
        ESCAPE : 27,
        TAB : 9,
        SHIFT : 16
    };



    var ComboboxSelector = function (combobox) {
        this.combobox = combobox;
        this.optionCount = 0;
        this.selectedIndex = -1;
        this.allSelectOptions = [];
        var selectorTop = combobox.textInputElement.outerHeight();
        var selectorWidth = combobox.textInputElement.outerWidth();
        this.selectorElement = jQuery(
            '<div class="combobox_selector" '+
            'style="display:none; width:'+selectorWidth+
            'px; position:absolute; left: 0; top: '+selectorTop+'px;"'+
            '></div>'
        ).insertAfter(this.combobox.textInputElement);
        var thisSelector = this;
        this.keypressHandler = function (e) {
            if (e.keyCode == Combobox.keys.DOWNARROW) {
                thisSelector.selectNext();
            } else if (e.keyCode == Combobox.keys.UPARROW) {
                thisSelector.selectPrevious();
            } else if (e.keyCode == Combobox.keys.ESCAPE) {
                thisSelector.hide();
                thisSelector.combobox.focus();
            } else if (e.keyCode == Combobox.keys.ENTER) {
                if(thisSelector.selectedIndex !== -1){
                    e.preventDefault();
                }
                thisSelector.combobox.setValue(thisSelector.getSelectedValue(),thisSelector.getSelectedKey());//vu.nguyen fix
                thisSelector.combobox.focus();
                thisSelector.hide();
            } else if(e.keyCode == Combobox.keys.TAB){
                thisSelector.hide();
				thisSelector.combobox.setValue(thisSelector.getSelectedValue(),thisSelector.getSelectedKey());//vu.nguyen fix
            }
        }

    };


    ComboboxSelector.prototype = {

        setSelectOptions : function (selectOptions) {
            this.allSelectOptions = selectOptions;
        },

		//vu.nguyen fix
		checkblank : function (blankkey) {
            this.comboboxBlank = 0;
			var check;

            if(blankkey!=undefined)
                check =  $('#'+blankkey).attr('combobox_blank');
            if(check!=undefined && check=='1')
                this.comboboxBlank = 1;

            // BaoNam 12/11/2013
            if( !this.comboboxBlank ){
                if(blankkey!=undefined)
                    check =  $('#'+blankkey).attr('readonly');
                if( !( check!=undefined && check=='readonly' ) )
                    this.comboboxBlank = 1;
            }
        },
		//end vu.nguyen fix

        buildSelectOptionList : function (startingLetters) {
            if (! startingLetters) {
                startingLetters = "";
            }
            this.unselect();
            this.selectorElement.empty();
            var selectOptions = [];
            this.selectedIndex = -1;
			//vu.nguyen fix: change array to object
            var i; var sum=0;
			//var testing = sortObject(this.allSelectOptions);
			//console.log(this.allSelectOptions);
            for (var i in this.allSelectOptions) {
                if (! startingLetters.length
                    || this.allSelectOptions[i].toLowerCase().indexOf(startingLetters.toLowerCase()) === 0)
                {
                    selectOptions[i] = this.allSelectOptions[i];
					sum++;
                }
            }

			this.optionCount = sum;

			//fix
			var ulElement = jQuery('<ul style="max-height:300px;"></ul>').appendTo(this.selectorElement);
			if(this.comboboxBlank!=1)
            	ulElement.append('<li value="" style="height:11px;"></li>');

			var strk;
            for (var i in selectOptions) {
				strk = i.replace("_jt@_","");
                ulElement.append('<li value="'+strk+'">'+selectOptions[i]+'</li>');
            }
			//end fix

            var thisSelector = this;
            this.selectorElement.find('li').click(function (e) {
                thisSelector.hide();
                // BaoNam 10/09/2013
                thisSelector.combobox.setValue(this.innerHTML, $(this).attr('value'));
                // end

                //thisSelector.combobox.focus();
            });
            this.selectorElement.mouseover(function (e) {
                thisSelector.unselect();
            });
            this.htmlClickHandler = function () {
                thisSelector.hide();
            };

        },

        show : function () {
            if (this.selectorElement.find('li').length < 1
                || this.selectorElement.is(':visible'))
            {
                return false;
            }
            jQuery('html').keydown(this.keypressHandler);
            this.selectorElement.show();
            jQuery('html').click(this.htmlClickHandler);
            return true;
        },

        hide : function () {
            jQuery('html').unbind('keydown', this.keypressHandler);
            jQuery('html').unbind('click', this.htmlClickHandler);
            this.selectorElement.unbind('click');
            this.unselect();
            this.selectorElement.hide();
        },

        selectNext : function () {
            var newSelectedIndex = this.selectedIndex + 1;
            if (newSelectedIndex > this.optionCount - 1) {
                newSelectedIndex = this.optionCount - 1;
            }
            this.select(newSelectedIndex);
        },

        selectPrevious : function () {
            var newSelectedIndex = this.selectedIndex - 1;
            if (newSelectedIndex < 0) {
                newSelectedIndex = 0;
            }
            this.select(newSelectedIndex);
        },

        select : function (index) {
            this.unselect();
        	this.selectorElement.find('li:eq('+index+')').addClass('selected');
        	this.selectedIndex = index;
        },

        unselect : function () {
        	this.selectorElement.find('li').removeClass('selected');
        	this.selectedIndex = -1;
        },

        getSelectedValue : function () {
            if(this.selectedIndex !== -1){
                return this.selectorElement.find('li').get(this.selectedIndex).innerHTML;
            } else {
				return this.combobox.textInputElement.val();
            }
        },
		//vu.nguyen add
		getSelectedKey : function () {
            if(this.selectedIndex !== -1){
                return this.selectorElement.find('li').get(this.selectedIndex).getAttribute("value");
            } else {
				return '';
            }
        }
		//end add

    };


})();