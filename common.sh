#!/bin/sh
PackageName=md-view
InstallPath=/mnt/HD/HD_a2/Nas_Prog/$PackageName
ExecFile=$InstallPath/tftp-now
LogFile=/tmp/cerbi_$PackageName.log
# This is a reference to the rc file. If CenterType=1, then the webroot is linked to /var/www/app
CenterType=1


apkg_log(){
	if [ -n "$1" ]; then
		# Output to console stdio
		echo "$PackageName: $1"
		# Output to log file
		if [ -n "$2" ]; then 
			if "$2" == "true"; then				
				echo "$PackageName: $1" >> $LogFile
			fi
		fi
	fi
}

apkg_logreset(){
	#This script is used to pre-run some commands if needed, before start installing an App.
	cat /dev/null > $LogFile
	apkg_log "MD-VIEW NEW LOG STARTED" true
}



app_install() {
	#Will copy files and install App to an appropriate folder.
	apkg_log "MD-VIEW INSTALL" true
	path_src=$1
	path_des=$2
	apkg_log "INSTALL: Install_Src  =\"$path_src\"" true
	apkg_log "INSTALL: Install_Dest =\"$path_des\"" true
	mv "$path_src" "$path_des"
}

app_init() {
	#Will create necessary symbolic links of installed App before being executed.
	apkg_log "MD-VIEW INIT" true
	DstPath=/var/www/$PackageName
	if [[ $CenterType -eq 1 ]]; then
		apkg_log "INIT: CenterType set to 1" true
		DstPath=/var/www/apps/$PackageName
	fi
	if ! [ -e $DstPath ]; then
		apkg_log "INIT: Setting WebUI stuff. Target path=\"$DstPath\"" true
		ln -s $InstallPath/private $DstPath
	fi
	# Handling extra custom linking here
	ln -s $InstallPath/public /var/www/$PackageName
}

app_start(){
	#Will start App daemon.
	apkg_log "MD-VIEW START" true
}

app_stop(){
	#Will stop App daemon.
	apkg_log "MD-VIEW STOP" true
}

app_clean(){
	#Will remove all links or files that created by init.
	apkg_log "MD-VIEW CLEAN" true
	DstPath=/var/www/$PackageName
	if [[ $CenterType -eq 1 ]]; then
		apkg_log "CLEAN: CenterType set to 1" true
		DstPath=/var/www/apps/$PackageName
	fi
	if [ -e $DstPath/ ]; then
		apkg_log "CLEAN: Removing WebUI stuff" true
		apkg_log "CLEAN: Removing $DstPath" true
		rm -f $DstPath/
	fi
	# Removing extra custom linkings here
	rm -f /var/www/$PackageName
}

app_remove(){
	#Will remove the installed App from hard drive.
	apkg_log "MD-VIEW REMOVE" true
	apkg_log "REMOVE: Target=\"$InstallPath\"" true
	rm -rf "$InstallPath"
}

app_preinst(){
	#This script is used to pre-run some commands if needed, before start installing an App.
	apkg_log "MD-VIEW PREINST" true
}
