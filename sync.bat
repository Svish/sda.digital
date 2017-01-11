@echo off

REM -preview 

pushd %~dp0
winscp /command //^
	"open ""sda.digital"""^
	"lcd %~dp0"^
	"synchronize remote -mirror -transfer=binary -delete -filemask="" | *.sublime-*; *.bat; _/; _new/;.*/"""^
	"rm "".cache"""^
	"exit"
echo.
popd
