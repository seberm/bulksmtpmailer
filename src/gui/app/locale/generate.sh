#!/bin/bash


for FILE in `ls *.po`; do

	NEWNAME=`echo $FILE | awk '{ match($0, /^(\w+).po/, t); print t[1]; }'`

	if [ "${?}" -ne "0" ]; then
		return 1
	fi

	msgfmt -o ${NEWNAME}.mo $FILE
done;
