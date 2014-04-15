#!/bin/bash

git ftp push www.technoparkcorp.com --user "${LOGIN}" --passwd "${PASSWORD}" --syncroot src --active
