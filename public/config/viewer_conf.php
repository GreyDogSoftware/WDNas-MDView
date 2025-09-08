<?php return array (
  'repositories' => 
  array (
    'repo_key' => '/shares/sdogo/Markdown',		// Simple repo. In simple mode, the repo_key becomes the name too.
    'repo_custom' =>  array('path'=>'/custom',	'description'=>'Custom set repo.', 'name'=>'Knowledge Base'), // Custom repo
	'repo_private' => array('path'=>'/private', 'description'=>'Password protected', 'name'=>'Private Stash','secret'=>'sdaf'), // Custom secured repo
  ),
  'allowed_extensions' => // Don't edit this.
  array (
    0 => 'md',
    1 => 'txt',
  ),
);?>