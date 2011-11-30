# Information Architecture to YML converter

This script grabs an IA and coverts it to YML, ready to rapidly load it to your pre-prod using silverstripe-testdata module.

## Requirements

- PHP
- silverstripe-testdata module to load the yml (it provides the circular reference processing capability)

## IA file format

- indent with \t to highlight parent-child relationships
- optional: include page type within << and >> to set the page type (otherwise it will default to "Page").

Example input (for example mysite.ia file):

	About us
		Mission statement
		Crew <<StaffHolder>>
			Laszlo <<StaffPage>>
			Ivan <<StaffPage>>
	Contact us <<UserDefinedForm>>

Run:

	php ia2yml.php mysite.ia > mysite/testdata/ia.yml

Output then is:

	Page:
		Aboutus1:
			Title: "About us"
		Missionstatement2:
			Title: "Mission statement"
			Parent: =>Page.Aboutus1
	StaffHolder:
		Crew3:
			Title: "Crew"
			Parent: =>Page.Aboutus1
	StaffPage:
		Laszlo4:
			Title: "Laszlo"
			Parent: =>StaffHolder.Crew3
		Ivan5:
			Title: "Ivan"
			Parent: =>StaffHolder.Crew3
	UserDefinedForm:
		Contactus6:
			Title: "Contact us"

And if you are using testdata, you can load it by visiting:

	<your site root>/dev/data/load/ia
