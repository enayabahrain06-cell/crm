# Role Management Reorganization - TODO

## Phase 1: Routes Update
- [x] 1.1 Update routes/web.php to nest roles under users
- [x] 1.2 Change route names from `admin.roles.*` to `admin.users.roles.*`

## Phase 2: Controller Updates
- [x] 2.1 Update RoleController to use new route names in redirects
- [x] 2.2 Update view paths in RoleController

## Phase 3: View Files Organization
- [x] 3.1 Create admin/users/roles/ directory structure
- [x] 3.2 Move role views to new location
- [x] 3.3 Update route references in role views

## Phase 4: Sidebar Navigation
- [x] 4.1 Remove "Roles" as separate top-level item
- [x] 4.2 Add "Roles" as sub-item under "Users"

## Phase 5: Testing
- [x] 5.1 Verify routes work correctly
- [x] 5.2 Verify sidebar navigation works
- [x] 5.3 Verify all CRUD operations on roles

