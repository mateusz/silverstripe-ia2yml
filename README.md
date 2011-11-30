# Information Architecture to YML converter

This script grabs an IA and coverts it to YML, ready to load usign silverstripe-testdata module.

## Requirements

- PHP
- silverstripe-testdata module to load the yml (it provides the circular reference processing capability)

## IA file format

- indent with \t to highlight parent-child relationships
- include <<PageType>> within the line to set the page type (the << and >> will be trimmed). Otherwise it will default to "Page".

Example input (.ia file):

	About us
		Mission statement
		Crew <<StaffHolder>>
			Laszlo <<StaffPage>>
			Ivan <<StaffPage>>
	Contact us <<UserDefinedForm>>

Run:

	php ia2yml.php | less

Output:

	Page:
		Aboutus1:
			Title: "About us"
	Page:
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
	StaffPage:
		Ivan5:
			Title: "Ivan"
			Parent: =>StaffHolder.Crew3
	UserDefinedForm:
		Contactus6:
			Title: "Contact us"

