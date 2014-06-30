function FormValidationController(formId) {
    var _this = this;

    this._formId = null;
    this._form = null;
    this._submitButton = null;
    this._isInvalid = false;

    this.init = function () {
        _this._formId = formId;
        _this._form = $('#' + formId);
        _this._initCorrespondingSubmitButton();
        _this._initListeners();
    };

    this._initCorrespondingSubmitButton = function () {
        $(':submit').each(function () {
            if ($(this).data('targetformid') === _this._formId) {
                _this._submitButton = this;
            }
            return false;
        });
    };

    this._initListeners = function () {
        $(_this._submitButton).on('click', function (event) {
            event.preventDefault();
            $('body').trigger('formSaveAttempt');
            _this._resetConfirmUnload();
            if (_this._hasHtml5Validation()) {
                $(_this._form).submit(function (event) {
                    if (!this.checkValidity()) {
                        event.preventDefault();
                        $(this).addClass('invalid');
                        _this._isInvalid = true;
                    } else {
                        $(this).removeClass('invalid');
                        _this._isInvalid = false;
                    }
                });
            }

            (_this._form).submit();
        });
        _this._initFormChangeListener();
    };

    //when a form value has changed, the user should be informed when leaving page without saving
    this._initFormChangeListener = function() {
        $(_this._form).find('input').each(function() {
            $(this).change(function() {
                _this._setConfirmUnload();
            });
        });
    };

    this._hasHtml5Validation = function () {
        return typeof document.createElement('input').checkValidity === 'function';
    };

    this._setConfirmUnload = function () {
        $(window).on('beforeunload', function() {
            return '';
        });
    };

    this._resetConfirmUnload = function() {
        $(window).off('beforeunload');
    };

    _this.init();
}