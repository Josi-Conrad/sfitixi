/**
 * Created by faustos on 19.05.14.
 */
function DrivingOrder() {
    var _this = this;

    this._lookaheadAddressFrom = null;
    this._lookaheadAddressTo = null;

    this._passengerId = null;

    this.init = function(lookaheadFromId, lookaheadToId, passengerId) {
        _this._passengerId = passengerId;
        _this._initLookaheadAddresses(lookaheadFromId, lookaheadToId);
    }

    this._initLookaheadAddresses = function(lookaheadFromId, lookaheadToId) {
        var _lookaheadFrom = new AddressLookahead(),
            _lookaheadTo = new AddressLookahead();
        _lookaheadFrom.init('lookahead_'+lookaheadFromId, _this._passengerId);
        _lookaheadTo.init('lookahead_'+lookaheadToId, _this._passengerId);
        _this._lookaheadAddressFrom = _lookaheadFrom;
        _this._lookaheadAddressTo = _lookaheadTo;
    }
}