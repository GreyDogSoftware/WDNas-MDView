const Nav={
    _PathHistory:[],
    _currentPathIndex:0,
    _loadCallback:null,
    _notificationsCallback:null,
    _APIEndpoint:'api/?act=get_files&path=',
    count: function(){
        return this._PathHistory.length;
    },
    isLast: function(){
        return (this._PathHistory.length===this._currentPathIndex)
    },
    isFirst: function(){
        return (this._currentPathIndex===1);
    },
    previous: function(){
        this.historyMove(-1);
    },
    current: function(){
        if(this._PathHistory.length>0){
            return this._PathHistory[this._currentPathIndex-1];
        }else{
            return null;
        }
    },
    next: function(){
        this.historyMove(1);
    },
    reload: function(){
        let _ref = this;
        if(this._PathHistory.length>0){
            this.makeRequest(this.current(), function(err, data){
                if (err !== 200) {
                    if(data['exit_message']){
                        _ref.showError('HTTP: Error' + err + "\n" +data['exit_message'],'Error');
                    }else{
                        _ref.showError('Something went wrong. HTTP' + err,'Error');
                    }
                }else{
                    this.load(data);
                }
            })
        }
    },
    reset: function(){
        this._PathHistory=[];
        this._currentPathIndex=0;
        this.raiseEvent();
    },
    explore: function(newPath){
        let _ref = this;
        this.makeRequest(newPath, function(err, data){
            if (err !== 200) {
                if(data['exit_code']){
                    if(data['exit_code']>=30 && data['exit_code']<=39){
                        offcanvasLeftObj.hide();
                        offcanvasRightObj.hide();
                        offcanvasTopObj.show();
                    }
                }else{
                    if(data['exit_message']){
                        _ref.showError('HTTP: Error' + err + "\n" +data['exit_message'],'Error');
                    }else{
                        _ref.showError('Something went wrong. HTTP' + err,'Error');
                    }
                }
            }else{
                // Are we at the end of the array?
                if(_ref._currentPathIndex===_ref._PathHistory.length){
                    // Yes
                    // Load the url here
                    _ref._PathHistory.push(newPath);
                    _ref._currentPathIndex++;
                    //this.load(this._PathHistory[this._currentPathIndex-1]);
                }else{
                    // No. We need to check if the next is the same as the new
                    if(_ref._PathHistory[_ref._currentPathIndex+1]===newPath){
                        // Yes, is the same.
                        _ref._currentPathIndex++;
                    }else{
                        // No. Then we need to delete every on front and insert the new node
                        // Deleting forward
                        _ref._PathHistory.splice(_ref._currentPathIndex,_ref._PathHistory.length-_ref._currentPathIndex);
                        _ref._currentPathIndex=_ref._PathHistory.length;
                        // Creating the new node
                        _ref._PathHistory.push(newPath);
                        _ref._currentPathIndex++;
                    }
                }
                // Load the data here
                _ref.load(data);
            }
        })
    },
    historyMove: function(indexCounter){
        let _ref = this;
        if (this._PathHistory.length>0){
            this._currentPathIndex=this._currentPathIndex+indexCounter;
            // Value clamping
            if (this._currentPathIndex<1){this._currentPathIndex=1};
            if (this._currentPathIndex>this._PathHistory.length){this._currentPathIndex=this._PathHistory.length};
            // Load the url here
            this.makeRequest(this._PathHistory[this._currentPathIndex-1], function(err, data){
                if (err !== 200) {
                    if(data['exit_message']){
                        _ref.showError('HTTP: Error' + err + "\n" +data['exit_message'],'Error');
                    }else{
                        _ref.showError('Something went wrong. HTTP' + err,'Error');
                    }
                }else{
                    _ref.load(data);
                }
            })
        }
    },
    load: function(url){
        //console.log(url);
        if(url){
            if(this._loadCallback){
                this._loadCallback(url);
                this.raiseEvent();
            }
        }
    },
    raiseEvent: function(){
        // Fire event
        // Since we are not using a HTMLElement as parent, we attach the
        // event to the main document.
        let event = new CustomEvent("nav-load", {bubbles: true, composed: true});
        document.dispatchEvent(event);
    },
    showNotification: function(body, title){
        if(this._notificationsCallback){
            if(typeof this._notificationsCallback.show !== 'function') { return;}
            this._notificationsCallback.show(body, title)
        }
    },
    showError: function(body, title){
        if(this._notificationsCallback){
            if(typeof this._notificationsCallback.showError !== 'function') { return;}
            this._notificationsCallback.showError(body, title)
        }
    },
    makeRequest: function(url, callback) {
        let xhr = new XMLHttpRequest();
        xhr.open('GET',this._APIEndpoint+url, true);
        xhr.responseType = 'json';
        xhr.onload = function() {
            var status = xhr.status;
            callback(status, xhr.response);
        };
        xhr.send();
    }
}