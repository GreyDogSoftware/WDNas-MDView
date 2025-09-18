var GetURL = function(url, callback) {
  var xhr = new XMLHttpRequest();
  xhr.open('GET', url, true);
  xhr.responseType = 'json';
  xhr.onload = function() {
    var status = xhr.status;
    callback(status, xhr.response);
  };
  xhr.send();
};



function LoadRepos(){
  var DstURL='api/?act=get_repos';
  GetURL(DstURL, function(err, data){
    if (err !== 200) {
      // Any error HTTP response code other than 200 is an error.
      console.log('Something went wrong: ' + err);
      Toast.showError('ERROR: Something went wrong. ' + err,'MD-View LoadRepos::ERROR')
    }else{
      if (data['exit_code']!==0){
        console.log('LoadRepos::ERROR: ' + data['exit_message']);
        Toast.showError(data['exit_message'],'MD-View LoadRepos::ERROR')
      }else{
        if (data['content'].length>0){
          let templateSource = document.getElementById("template_repoItem");
          var target = document.getElementById("doc_list");
          target.innerHTML="";
          for (var x=0; x < data['content'].length; x++){
            if (data['content'][x]['file_type']){
              let repoContainer = templateSource.content.cloneNode(true).querySelector('li');

              let repoNameTarget = repoContainer.querySelector('span#template-repo-name');
              repoNameTarget.innerText = data['content'][x]['file_name'];	// Setting the link text

              let repoDescTarget = repoContainer.querySelector('span#template-repo-description');
              repoDescTarget.innerHTML = '&nbsp;'; // Needs to be set as html or won't work
              if(data['content'][x]['description']){repoDescTarget.innerText=data['content'][x]['description']}

              let repoIcon ='bi bi-database'; // Default icon
              let repoSecured = data['content'][x]['protected'];
              let repoAvailable = data['content'][x]['available'];

              if(repoSecured){repoIcon='bi bi-database-lock'}
              if(!repoAvailable){repoIcon='bi bi-database-slash'}

              let repoIconTarget = repoContainer.querySelector('i.template-item-icon');
              repoIconTarget.setAttribute('class', repoIcon);	// Setting the link icon

              if(repoAvailable){
                //let currentAction = "LoadFiles('"+data['content'][x]['path_rela']+"')";
                let currentAction = "Nav.explore('"+data['content'][x]['path_rela']+"')";
                let repoActionTarget = repoContainer.querySelector('a');
                repoActionTarget.setAttribute("onclick", currentAction); // Setting the link action
              }

              // Setting some extra properties
              repoContainer.setAttribute("repo-data-path", data['content'][x]['path_rela']);
              repoContainer.setAttribute("repo-data-secured", data['content'][x]['protected']);
              repoContainer.setAttribute("repo-data-available", data['content'][x]['available']);
              target.appendChild(repoContainer);	// Appending new children to DOM
            }
          }
          Nav.reset();
        }
      }
    }
  });
}

const LoadMD = function(url,filename) {
  let refreshButton = document.getElementById("doc-refresh");
  refreshButton.setAttribute("doc-file-name", filename);
  refreshButton.setAttribute("doc-source",    url);
  let shareButton = document.getElementById("doc-share");
  shareButton.setAttribute("doc-source",    url);
  let rawButton = document.getElementById("doc-raw");
  rawButton.setAttribute("doc-source",    url);

  var target_content = document.getElementById("doc_content");
  // The server streams the file content directly, so there's no parsin involved.
  target_content.setAttribute("src", 'api/?act=get_content&path='+url);
  var target_name = document.getElementById("doc_filename");
  if(filename){
    target_name.innerHTML=filename;
  }else{
    target_name.innerHTML="";
  }
  File.load(url);
};

const LoadFiles=function(source){
  var DstURL='api/?act=get_files&path='+source;
  GetURL(DstURL, function(err, data){
    if (err !== 200) {
      console.log('Something went wrong: ' + err);
      if(data['exit_message']){
        Toast.showError(data['exit_message'],'MD-View LoadFiles::ERROR')
      }else{
        Toast.showError('Something went wrong. HTTP' + err,'MD-View LoadFiles::ERROR')
      }
    }else{
      if (data['content'].length>0){
        let templateSource = document.getElementById("template_menuItem");
        var target = document.getElementById("doc_list");
        target.innerHTML="";
        for (var x=0; x < data['content'].length; x++){
          if (data['content'][x]['file_type']){
            let div = templateSource.content.cloneNode(true).querySelector('li');
            let item_name = div.querySelector('span.template-item-text');
            let item_icon = div.querySelector('i.template-item-icon');
            let item_action = div.querySelector('a');

            let currentIcon, currentText, currentAction;
            if (data['content'][x]['file_type']=='file'){
              // File
              currentAction= "LoadMD('"+data['content'][x]['path_rela']+"','"+data['content'][x]['file_name']+"')";
              currentIcon="bi bi-file-earmark-code";
            }else if (data['content'][x]['file_type']=='dir'){
              // Folder
              //currentAction= "LoadFiles('"+data['content'][x]['path_rela']+"')";
              currentAction= "Nav.explore('"+data['content'][x]['path_rela']+"')";
              currentIcon="bi bi-folder";
            }else{
              // Empty
              currentIcon="bi bi-ban";
            }

            item_name.textContent = data['content'][x]['file_name'];	// Setting the link text
            item_icon.setAttribute("class", currentIcon);	// Setting the link icon
            item_action.setAttribute("onclick", currentAction); // Setting the link action
            target.appendChild(div);	// Appending new children to DOM
          }
        }
      }
    }
  });
}

var refreshDocument = function(){
  let refreshButton = document.getElementById("doc-refresh");
  if (refreshButton){
    let sourceURL = refreshButton.getAttribute('doc-source');
    if (sourceURL){
      // Checking if the source URL is set
      let target_content = document.getElementById("doc_content");
      //target_content.innerHTML = ""; // Setting the content to empty
      target_content.setAttribute("src", '');
      target_content.setAttribute("src", 'api/?act=get_content&path='+sourceURL);
    }
  }
}

var shareDocument = function(){
  let refreshButton = document.getElementById("doc-share");
  if (refreshButton){
    let sourceURL = refreshButton.getAttribute('doc-source');
    if (sourceURL){
      // Checking if the source URL is set
      let host = window.location.protocol + '//' + window.location.host + window.location.pathname + '?doc=' + sourceURL;
      clipboardHandler.copyText(host);
    }
  }
}

var documentViewRaw = function(){
  let rawButton = document.getElementById("doc-raw");
  if (rawButton){
    let sourceURL = rawButton.getAttribute('doc-source');
    if (sourceURL){
      let targetURL = window.location.protocol + '//' + window.location.host + window.location.pathname + 'api?act=get_content&path=' + sourceURL;
      console.log(window.location);
      console.log(targetURL);
      window.open(targetURL, '_blank').focus();
    }
  }
}

function LoadFromURL() {
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
    if (pair[0] == 'doc') {
      if(pair[1]){
        LoadMD(pair[1])
      }
    }
  }
}
LoadFromURL();

const Nav={
  _PathHistory:[],
  _currentPathIndex:0,
  _navCallback:null,
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
    this.load(this.current());
  },
  reset: function(){
    this._PathHistory=[];
    this._currentPathIndex=0;
    this.raiseEvent();
  },
  explore: function(newPath){
    // Are we at the end of the array?
    if(this._currentPathIndex===this._PathHistory.length){
      // Yes
      this._PathHistory.push(newPath);
      this._currentPathIndex++;
      // Load the url here
      this.load(this._PathHistory[this._currentPathIndex-1]);
      //return this._PathHistory[this._currentPathIndex];
    }else{
      // No. We need to check if the next is the same as the new
      if(this._PathHistory[this._currentPathIndex+1]===newPath){
        // Yes, is the same.
        this._currentPathIndex++;
        // Load the url here
        this.load(this._PathHistory[this._currentPathIndex-1]);
        //return this._PathHistory[this._currentPathIndex];
      }else{
        // No. Then we need to delete every on front and insert the new node
        // Deleting forward
        this._PathHistory.splice(this._currentPathIndex,this._PathHistory.length-this._currentPathIndex);
        this._currentPathIndex=this._PathHistory.length;
        // Creating the new node
        this._PathHistory.push(newPath);
        this._currentPathIndex++;
        // Load the url here
        this.load(this._PathHistory[this._currentPathIndex-1]);
        //return this._PathHistory[this._currentPathIndex];
      }
    }
  },
  historyMove: function(indexCounter){
    if (this._PathHistory.length>0){
      this._currentPathIndex=this._currentPathIndex+indexCounter;
      // Value clamping
      if (this._currentPathIndex<1){this._currentPathIndex=1};
      if (this._currentPathIndex>this._PathHistory.length){this._currentPathIndex=this._PathHistory.length};
      // Load the url here
      this.load(this._PathHistory[this._currentPathIndex-1]);
    }
  },
  load: function(url){
    //console.log(url);
    if(url){
      if(this._navCallback){
        this._navCallback(url);
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
  }
}

const File={
  name: '',
  fullname: '',
  path: '',
  size: 0,
  extension: '',
  exists: false,
  protected: false,
  load: function(filePath){
    let _ref = this; // Keeping a reference to the main object instance
    console.log('File.load: ' + filePath);
    // Since this function requires an external endpoint to work
    // there's zero trust on the data. Everything should be validated.
    var DstURL='api/?act=get_fileinfo&path='+filePath;
    GetURL(DstURL, function(err, data){
      if (err !== 200) {
        // Any error HTTP response code other than 200 is an error.
        console.log('Something went wrong: ' + err);
        Toast.showError('ERROR: Something went wrong. ' + err,'MD-View LoadRepos::ERROR')
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
  }
}
