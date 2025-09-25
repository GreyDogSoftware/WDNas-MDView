if (typeof makeResquest === 'undefined'){
    makeResquest = function(url, callback) {
        let xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.responseType = 'json';
        xhr.onload = function() {
        var status = xhr.status;
            callback(status, xhr.response);
        };
        xhr.send();
    };
}

if (typeof getConfig === 'undefined'){
    getConfig=function(){
        let _ref = this; // Keeping a reference to the main object instance
        let _APIEndpoint = '/apps/md-view/api?act=getconfig';
        makeResquest(_APIEndpoint,function(status, data){
            if(status!=200){
                console.log(status);
            }else{
                if(!'exit_code' in data){ return;}
                if(!'content' in data){ return;}
                if(data['exit_code']===0){
                    if(!'repositories' in data['content']){ return;}
                    //if(!'allowed_extensions' in data['content']){ return;}
                    let templateSource = document.getElementById("template_menuItem");
                    let listTarget = document.getElementById("repo-list");
                    let _refRepoList = data['content']['repositories']
                    if (listTarget){
                        //console.log('conf_ok');
                        //console.log(Object.keys(_refRepoList).length);
                        if(Object.keys(_refRepoList).length==0){
                            listTarget.innerHTML='<tr><td colspan="3"><center>No repositories configured</center></td></tr>';
                        }else{
                            for (var x=0; x < Object.keys(_refRepoList).length; x++){
                                let objKey = Object.keys(_refRepoList)[x];
                                let repoTemplate = templateSource.content.cloneNode(true).querySelector('tr');
                                let _refRepo = _refRepoList[objKey];
                                let nameTarget = repoTemplate.querySelector('td.repo-row-name');
                                let descTarget = repoTemplate.querySelector('td.repo-row-description');
                                let actiTarget = repoTemplate.querySelector('td.repo-row-actions');
                                if(nameTarget){nameTarget.innerText=_refRepo['name']}
                                if(descTarget){descTarget.innerText=_refRepo['description']}
                                //if(actiTarget){actiTarget.innerText=_refRepo['path']}
                                if(actiTarget){
                                    if(_refRepo['secret']!=''){actiTarget.innerText='Secured'}else{actiTarget.innerText=''}
                                }
                                listTarget.appendChild(repoTemplate);
                            }
                        }
                    }
                }
            }
        });
    };
}