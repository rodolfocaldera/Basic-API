# REST API
## Description
This is a basic REST API on PHP

## Sections
### Create user
#### Route
`HOST/API/user`

### Description
To Create a user, you'd to take to account the next data needed:
- name (letters only)
- last name (letters only)
- password (alphanumeric only)
- email
- role (numbers from 1 to 5, which means 1 for the basic role and 5 for the high)


## Roles
- basic: only access
- medium: access and query permission
- high medium: access and insert permission
- medium high: CRUD permisions except delete
- high: CRUD permission