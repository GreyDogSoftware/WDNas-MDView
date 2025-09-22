const File={
  name: '',
  fullname: '',
  path: '',
  size: 0,
  extension: '',
  exists: false,
  protected: false,
  _APIEndpoint:'api/?act=get_fileinfo&path=',
  _notificationsCallback:null,
  load: function(filePath){
    let _ref = this; // Keeping a reference to the main object instance
    console.log('File.load: ' + filePath);
    // Since this function requires an external endpoint to work
    // there's zero trust on the data. Everything should be validated.
    var DstURL=_ref._APIEndpoint+filePath;
    _ref.makeRequest(DstURL, function(err, data){
      if (err !== 200) {
        // Any error HTTP response code other than 200 is an error.
        console.log('Something went wrong: ' + err);
        _ref.showError('Something went wrong. HTTP' + err,'ERROR')
      }else{
        if(!'exit_code' in data){ return;}
        if(!'content' in data){ return;}

        if(data['exit_code']==0){
          if(!'name' in data['content']){ return;}
          if(!'fullname' in data['content']){ return;}
          if(!'path' in data['content']){ return;}
          if(!'size' in data['content']){ return;}
          if(!'extension' in data['content']){ return;}
          if(!'exists' in data['content']){ return;}
          if(!'protected' in data['content']){ return;}

          _ref.name = data['content']['name'];
          _ref.fullname = data['content']['fullname'];
          _ref.path = data['content']['path'];
          _ref.size = data['content']['size'];
          _ref.extension = data['content']['extension'];
          _ref.exists = data['content']['exists'];
          _ref.protected = data['content']['protected'];

          _ref.raiseEvent();
        }
      }
    });
  },
  raiseEvent: function(){
    // Fire event
    // Since we are not using a HTMLElement as parent, we attach the
    // event to the main document.
    let event = new CustomEvent("file-load", {bubbles: true, composed: true});
    document.dispatchEvent(event);
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
  }
}