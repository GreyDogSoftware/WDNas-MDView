const GetURL = function(url, callback) {
  let xhr = new XMLHttpRequest();
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
            if (data['content'][x]['type']){
              let repoContainer = templateSource.content.cloneNode(true).querySelector('li');

              let repoNameTarget = repoContainer.querySelector('span#template-repo-name');
              repoNameTarget.innerText = data['content'][x]['name'];	// Setting the link text

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
                let currentAction = "Nav.explore('"+data['content'][x]['relative']+"')";
                let repoActionTarget = repoContainer.querySelector('a');
                repoActionTarget.setAttribute("onclick", currentAction); // Setting the link action
              }

              // Setting some extra properties
              repoContainer.setAttribute("repo-data-path", data['content'][x]['relative']);
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

const LoadMD = function(url) {
  let refreshButton = document.getElementById("doc-refresh");
  refreshButton.setAttribute("doc-source",    url);
  let shareButton = document.getElementById("doc-share");
  shareButton.setAttribute("doc-source",    url);
  let rawButton = document.getElementById("doc-raw");
  rawButton.setAttribute("doc-source",    url);

  var target_content = document.getElementById("doc_content");
  // The server streams the file content directly, so there's no parsin involved.
  target_content.setAttribute("src", 'api/?act=get_content&path='+url);
  File.load(url);
};

const loadFileTree=function(data){
  if (data['content'].length>0){
    let templateSource = document.getElementById("template_menuItem");
    var target = document.getElementById("doc_list");
    target.innerHTML="";
    for (var x=0; x < data['content'].length; x++){
      if (data['content'][x]['type']){
        let div = templateSource.content.cloneNode(true).querySelector('li');
        let item_name = div.querySelector('span.template-item-text');
        let item_icon = div.querySelector('i.template-item-icon');
        let item_action = div.querySelector('a');

        let currentIcon, currentText, currentAction;
        if (data['content'][x]['type']=='file'){
          // File
          currentAction= "LoadMD('"+data['content'][x]['relative']+"')";
          currentIcon="bi bi-file-earmark-code";
        }else if (data['content'][x]['type']=='dir'){
          // Folder
          //currentAction= "LoadFiles('"+data['content'][x]['relative']+"')";
          currentAction= "Nav.explore('"+data['content'][x]['relative']+"')";
          currentIcon="bi bi-folder";
        }else{
          // Empty
          currentIcon="bi bi-ban";
        }

        item_name.textContent = data['content'][x]['name'];	// Setting the link text
        item_icon.setAttribute("class", currentIcon);	// Setting the link icon
        item_action.setAttribute("onclick", currentAction); // Setting the link action
        target.appendChild(div);	// Appending new children to DOM
      }
    }
  }
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

