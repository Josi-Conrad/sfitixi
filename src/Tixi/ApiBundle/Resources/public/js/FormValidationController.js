function FormValidationController(formId) {
    var _this = this;

    this._formId = null;
    this._form = null;
    this._submitButton = null;
    this._isInvalid = false;

    this.init = function() {
        _this._formId = formId;
        _this._form = $('#'+formId);
        _this._initCorrespondingSubmitButton();
        _this._initListeners();
    }

    this._initCorrespondingSubmitButton = function(){
        $(':submit').each(function() {
            if($(this).data('targetformid')===_this._formId) {
                _this._submitButton = this;
            }
            return false;
        });
    }

    this._initListeners = function() {
        $(_this._submitButton).on('click', function(event) {
            event.preventDefault();
            if(_this._hasHtml5Validation()) {
                $(_this._form).submit(function(event) {
                    if(!this.checkValidity()) {
                        event.preventDefault();
                        $(this).addClass('invalid');
                        _this._isInvalid = true;
                    }else {
                        $(this).removeClass('invalid');
                        _this._isInvalid = false;
                    }
                });
            }
            (_this._form).submit();
        });
    }

    this._hasHtml5Validation = function() {
        return typeof document.createElement('input').checkValidity === 'function';
    }

    _this.init();


}