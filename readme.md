# REST API
## Description
This is a basic REST API on PHP

## Sections
## Login
### Route
`HOST/API/log`

### Method
`POST`

### Description
Data needed:
- email
- password

### Response
Token needed for future requests

### Create user
#### Route
`HOST/API/user`

### Method
`POST`

### Description
To Create a user, you'd to take to account the next data needed:
- name (letters only)
- last name (letters only)
- password (alphanumeric only)
- email
- role (numbers from 1 to 5, which means 1 for the basic role and 5 for the high)

### Create post
#### Route
`HOST/API/post`

### Method
`POST`

### Description
To Create a post, you'd to take to account the next data needed:
- Token
- Title (alphanumeric only)
- Description (alphanumeric only)


## Roles
- basic: only access
- medium: access and query permission
- high medium: access and insert permission
- medium high: CRUD permisions except delete
- high: CRUD permission

### Delete post
#### Route
`HOST/API/post`

### Method
`DELETE`

### Description
To delete a post, you'd to take to account the next data needed:
- Token
- id

### Update post
#### Route
`HOST/API/post`

### Method
`PUT`

### Description
To update a post, you'd to take to account the next data needed:
- Token
- id
- title
- description

### get all posts
#### Route
`HOST/API/post`

### Method
`GET`

### Description
To get all posts, you'd to take to account the next data needed:
- Token


## Roles
- basic: only access
- medium: access and query permission
- high medium: access and insert permission
- medium high: CRUD permisions except delete
- high: CRUD permission