# Changelog
All notable changes to this project will be documented in this file.

## 0.8 - Helgö (Unreleased)
### Added
- Support to moderate additions/changes made by specific (moderated) roles
### Fixed
- Some attribute changes not saved, when one was a delete operation

## 0.7.4
### Added
- Bing Maps Support
- [Iconclass](http://iconclass.org)-Attribute
- Re-add dependency lock-files
### Fixed
- Accidentally moving map outside viewport
- Ignore uppercase/lowercase letters in login form user names
- Overwriting existing files/thumbnails
- Error on file loading due to invalid exif data
### Changed
- Select newly created layer in Layer Editor

## 0.7.3
### Added
- CLI command to create new users/roles
- Login using a nickname instead of e-mail address
### Fixed
- Weird layout for some attribute lists
- Do not open tree elements instantly when hovering while drag&drop (a delay of 500ms is now used/respected)
- Dropping entities when tree is scrolled
- Do not allow to save a user/role without required fields set
- Set max zoom level on map fit to fix blank map layer due to unsupported layer zoom
- "Send Reset-Mail" now actually sends reset mails (Please see [INSTALL.md](INSTALL.md#send-mails) for further details on how to set this up)
### Changed
- Replace JS bibtex-parser
- Replace debounce package with lodash's

## 0.7.2
### Changed
- Make debounce delay in TypeaheadSearch component a prop and increase default value (`250ms` => `500ms`)
### Fixed
- Clear queue before sending another request in typeahead searches (should speed up tree search and others with large data sets)

## 0.7.1
### Added
- Seeder for basic map setup
### Fixed
- Replaced function calls of old roles package in RolesPermissionsSeeder and AdminUserSeeder

## 0.7 - Giza
### Added
- Time Period datatype (epoch datatype without dropdown)
- Screencast functionality (in Tools menu)
  - Save local or upload to Spacialist
- File modal
  - 3D editor
    - Click on objects to select
      - Move, rotate, scale objects using handler or GUI
      - Press `f` to focus on object
      - Add LOD support
  - Images
    - Image manipulation
      - Supports several filters (blur, sharpen, color-based filters, ...)
      - Manipulations can be stored
  - PDF
    - print button
  - OCR support (PDF and Images)
- Dynamic Parent-Element for `string-sc` (Single Choice Dropdown) datatype
  - Reference another SCD attribute in Data-Model-Editor to use it's value as Parent-Element
- Parents Breadcrumb added to Entity Detail view header
- Allow width expansion of attributes (Currently supported: Tabular)
- PHPUnit tests (see [README.md](README.md#testing) for further instructions)
- Option to set root element, entity-type and column as entity name in GIS importer
- Also search for author in reference modal
### Changed
- Use [Tree-Shaking](https://webpack.js.org/guides/tree-shaking/) in supported modules
- Improved fullscreen handler code
- Update to Laravel 6.2
- Rework search components (should no longer logout on fast typing)
- Switched (unmaintained) permission package
- Dependency updates
### Fixed
- Do not fire upload-from-clipboard event in input fields
- Update references in Reference Tab, Attribute Icons in Entity Detail View and Reference Modal
- File modal container height
- Conversion for image formats not supported by PHP
- Several styles and translations
- Popup not working for newly added and linked geodata on click on linked entity
- Adding first entity with a serial attribute
- Sub-Entity counter for first added sub-entity
- Linking geodata to entity-types with allowed geometry type 'any'
- Hide file export checkbox/button if user doesn't have permission
- Reset fields in add entity-type modal after closing modal
- AD/BC dropdowns overlapping dropdowns of other attributes
- Non-visible selection list for entity search in file linking tab
- Overflowing container in file upload modal (upload from clipboard)
- Storing dates in epoch (if start and end are BC)
- Version Info modal after Login (without reload)
- Several fixes in the Geometry Datatype modal
### Removed/Deprecated
- Unused/unmaintained packages

## 0.6.3
### Added
- Entity as table column datatype
- Entity type to entity detail form header
- Demo Seeder (See [INSTALL.md](https://github.com/eScienceCenter/Spacialist/blob/0.6.3/INSTALL.md#migrations) for more details)
- Show notification if attribute is deleted
- Color indicator (based on entity layer color) to tree and entity-detail view (replaces monument icon)
- Apply new preference values without reload
- Editmode to upload from clipboard
- Switch between card and list layout in file viewer
### Changed
- Sub-Entity count in tree is now displayed inside color indicator
- Cleaned up geodata popup (Removed coordinate table, added length/area of geodata)
- Moved file name to bottom of card, made font size smaller, display file name on hover
- Reworked map popups (supports dynamic content such as hideable coordinate list; no more "click through")
### Fixed
- Use color picker for simple layer styling (GIS)
- Deleting geodata on map
- User locale after login
- Font loading
- Error on empty layer array in map
- Unset preference handling
- Use user's language/default language for retrieved concepts
- Jump to selected element in tree
- Empty table row after add
- Search in other available languages for labels that have no match in user's language (to make concepts available as label that are not yet translated)
- Align user menu dropdown to the right, so dropdown is not displayed outside document bounds
- Position of user menu dropdown
- Polishing file list layout
- Compatibility with latest version of vue (2.6)
- File modal container height
### Removed/Deprecated
- Monument icon in tree and entity-detail view

## 0.6.2
### Fixed
- Bug in migrations causing errors on saving new values

## 0.6.1
### Added
- Scaling control to 3D file viewer
### Fixed
- Missing content in Chrome/WebKit/Blink browsers
- Migration from previous version
- Links to Data Analysis-Plugins
- Several style issues

## 0.6 - Federsee
This version is a complete rewrite using Laravel and Vue.js. Please refer to the [INSTALL.md](INSTALL.md) for migration and new setup information.
### Added
- File Viewer
  - Simple Office Documents Viewer (as HTML-Text)
  - Edit Mode for text and XML files
  - Replace single files (even with different file types)
  - Rendering of HTML files
  - DICOM Support
  - Rename files
  - 3D-Viewer can now load all 3D files of sub-entities into same scene
  - New audio plugin (based on [wavesurfer](https://wavesurfer-js.org), visualization and EQ)
  - Simple navigation to jump to previous/next file right from the modal (using buttons or left/right arrow key)
  - Icons in the upper right corner to indicate whether this file is linked and has tags
  - Checkboxes in the upper left corner to select files for export/download
  - Properties (copyright and description) and tags fields to upload page, to set them for all uploaded files
  - Shortcut (`ctrl + v`) to directly upload files from clipboard
- Tree View
  - Reorder buttons (by rank (default), name, entity-type, children count)
- Welcome Page
  - Maintainer (Name and E-Mail-Address), Project Description and Access (Public/Private) can be configured in settings
- Reference Modal
  - Options (Edit/Delete) to reference list
- Bibliography
  - Export BibTeX
  - 'Hide BibTeX metadata fields' toggle in Bibliography view
  - Auto-Fill from clipboard (`ctrl + v`) in new/edit entry modal
- Attribute Types
  - SQL Type
    - Rendered as Table or single value
    - Supports translations (Use `concept_url` as header/content)
    - Supports `:entity_id` as placeholder for current selected entity
  - Serial Type (Auto-incrementing ID)
- Geometry Preference (EPSG-Code). E.g. to display coordinates in popups different from EPSG:4326
- Measurement tool for map
- Attribute Dependencies
  - Attributes can now depend (are visible/invisible) on values of other attributes
- GIS View
  - QGIS-like styling (categorized, graduated (equal interval and quantile) and color) and labeling
- Contributors in about modal
- Data-Model-Editor
  - Duplicate Entity-Types
  - Option to restrict options for dropdowns
- Info texts on hover to icon-only labeled buttons/links/etc.
- Set user's language to browser's default in user settings
### Changed
- Moved from Lumen (5.3) to Laravel (5.7)
- Moved from AngularJS (1.5) to Vue.js (2.5)
- Moved from LeafletJS (1.0) to OpenLayers (5.2)
- Updated Bootstrap 3.3 to 4.1
- Switched from Material Design back to original Bootstrap
- Switched from Material Icons to new FontAwesome 5.5
- Moved Plugin-like parts to real Plugins
- Certainty Modal now has 3 icons to view information without opening the modal
  - !-Icon: Always displayed. Color based on certainty level
  - Comment-Icon: Displayed if there is a comment
  - Bookmark-Icon: Displayed if there is at least one reference
- User/Role Management bundle several actions (Save, Edit, Delete, ...) in single dropdown (...-menu)
- Adding/Reorder attributes in Context-Type tab in Data-Model-Editor is now done using Drag&Drop.
- File Viewer
  - Load files as chunk of 15
  - Filter by ... for each tab (Linked, Unlinked, All Files)
    - Filetype
    - Camera
    - Date
  - Link tab revamped
    - Added option to link to entities from a search bar
    - Added 'Unlink/Link from/to entity' button in
- Tree View
  - Now loads root elements only. Sub-elements are loaded on request
- Bibliography
  - Only loads the first 20 entries. More entries are loaded on scroll.
  - Dropped differentiation between mandatory and optional fields (all are optional now).
- Tree Search is now async and matching entities can be selected from a list (and expanded/highlighted)
- Global search based on relevance. Also supports bangs for different categories:
  - `!e ` + Search term: Entities
  - `!f ` + Search term: Files
  - `!g ` + Search term: Geodata
  - `!b ` + Search term: Bibliography
### Fixed
- 3D-File-Viewer: Mouse Controls now work even if WebVR is available (but not active)
### Removed/Deprecated
- Links: The links have changed, but they will continue to work. We recommend to update your bookmarks, because the old link structure is now deprecated
- Edit Mode (Column sizes can still be modified in preferences)

## 0.5.1
### Added
- Only show up to first 10 rows in csv preview
- Add option to parse csv without header row
- Add help icon to header (links to github wiki)
- Allow to clear selected epoch
- Submit buttons to most forms (hit enter to submit)
- Version info/about popup in header bar (settings menu)
### Changed
- Revamp header bar
### Fixed
- Tree search (deleting last char in search didn't show whole tree)
- Height of containters (global scroll bar should be gone)
- Missing map

## 0.5 - Ephesus
### Added
- GIS View
  - Importer
    - CSV/DSV
    - WKT (as CSV)
    - KML/KMZ
    - Shape Files
    - GeoJSON
  - Export layers
    - GeoJSON (default)
    - CSV
    - WKT (as CSV)
    - KML/KMZ
    - GML
  - Layer options
- Analysis View (Beta)
  - SQL-like expert mode
  - simple non-SQL mode (default)
  - Visualizations based on [plot.ly](https://plot.ly/)
    - Scatter
    - Line
    - Bar
    - Pie
    - Histogram
    - Ternary
  - Export
    - as CSV (ambiguous columns are currently not supported)

## 0.4.4
### Fixed
- Error on empty Geo Objects
- Fix Tree Search
- Fix empty and non-working filter in files tab
- Map (and files, if both deactivated) was still visible/selected, if deactivated
- Fix tag section in file modal

## 0.4.3
### Added
- Spinner icon to login screen as indicator for loading data (For some instances it can take several seconds to load the required data)
### Fixed
- Roles/Permissions are now only seeded if they don't exist yet (It was not possible to add new permissions to existing projects without reseed everything)
- Catch exception in exif parser to keep Spacialist running even if exif parsing failed
- Check if file exists before parsing exif to keep Spacialist running even if a file gets lost on disk, but is still present in the database.

## 0.4.2
### Added
- New supported filetypes
  - `.pdb` files (Protein Data Bank)
  - compressed files (e.g. `.zip`, `.rar`, `.tar`)
- Download button in the file viewer
### Fixed
- Delete Context Modal didn't get closed and context was not removed (see #292)
- Add disabled state to round _Save_ button and _Unstaged Changes_ Popup (see #293)
- Better filetype handling.

## 0.4.1
### Added
- New Datatype: Table
### Fixed
- A couple of bugs in epoch datatype (it was possible to store non-integer data and data with a start date > end date)
- Context form must be valid to store context
- Error after adding a literature to the _Additional Information_ popup
- Fixed a drag&drop bug in the context tree (see #255)

## 0.4 - Delphi
### Added
- Preferences to allow customization for admins and users as well
- Global search, Supports
  - **Contexts** by...
    - name
    - lasteditor
    - updated_at (supported format is `MM.DD.YYYY <DayName> <MonthName>`)
    - Context-Type
    - Attributes (Values and Labels)
  - **Files** by...
    - Tags
    - Copyright
    - Description
    - lasteditor
  - **Layers** by...
    - Name
    - URL
  - **Sources** _(click on the star icon in the results to jump to the source entry)_ by...
    - description
    - lasteditor (literature, source and context)
  - **Literature** by...
    - all fields
  - **Users** by...
    - Name
    - E-Mail
### Fixed
- _Unstaged Changes_ Popup is no longer displayed when reference icon (the star icon) next to an attribute of the same context is clicked
- Description for Literature References Entry is no longer required
- State changes after a file popup is closed is no longer buggy (clicking another file before the page was reloaded led to weird behaviour)
- Saving a context no longer requires a page reload
- Flickering of the References tab in the right-hand view (map, files, ...) is fixed

## 0.3.5
### Added
- Display csv as table (with support for different delimiters)
- Display context type's layertype in Data Model Editor
- citation key in bibliography table (update/overwrite now based on citation key)
- Vive support for 3D viewer
- Display reference-indicator if certainty is changed

## 0.3.4
### Added
- List linked contexts in file popup
- Exif Data viewer for image files (JPEG and TIFF)
- Display coordinates in DMS (Degree Minute Second) in marker popup
### Fixed
- Fix missing values of metadata in file popup
- Marker popup form styles
- Opacity of layers (Storing was not possible, stored value was ignored)

## 0.3.3
### Fixed
- use current selected language for search requests
- encode search term in context search (fix missing results for terms with special chars, e.g. umlauts)

## 0.3.2
### Fixed
- The fab-styled save button (context detail view upper right corner) didn't work
- The _Last modified_ info was not updated after saving the context
- The _Toggle Edit Mode_ button is now disabled on not-supported states (should only be visible in the main state with the tree, detail view and map/files)

## 0.3.1

### Fixed
- Context Search (didn't work at all)
- Possible Error retrieving files without a thumbnail (every file except images)

## 0.3 - Cannae

### Added
- Drag & Drop Support for Element Tree
- More Datatypes (Integer, Geography, Slider, Boolean)
- Dynamic UI (The three columns on the main view can be shrinked/extended, enable Edit Mode in the Settings Dropdown menu)
- Basic Unit-Testing (Lumen API)
- State-based Routing using [ui-router](https://github.com/angular-ui/ui-router)
  - Shareable Links
  - Redirect to last state after (re)login
- Unset Selected Values (`string-sc` aka Single Dropdown)
### Changed
- Photo Viewer => File Viewer (Please read section _Protected Files_ in [INSTALL.md](INSTALL.md) for proper setup)
  - PDFs ([pdf.js](https://github.com/mozilla/pdf.js) & [angularjs-pdf](https://github.com/sayanee/angularjs-pdf))
  - Text documents (including text highlighting using [highlight.js](https://highlightjs.org/))
  - 3D files (`.dae` and `.obj`/`.mtl`, using [three.js](https://github.com/mrdoob/three.js/))
  - Markdown files ([marked](https://github.com/chjj/marked) & [angular-marked](https://github.com/Hypercubed/angular-marked))
  - Audio/Video files
- Remove input field placeholders
- Layer Control
  - Add layers for all existing contexttypes + one layer for unlinked geodata objects
  - Contexttype layers are restricted to a type (either MultiPoint, MultiLinestring or Multipolygon)
  - Geodata snapping
  - Add Bing and Google Maps as Tile-Provider
  - Layer Editor
    - Change color
    - Enable/Disable layers (**only** overlays, on startup)
    - Set opacity
    - Set default baselayer
  - Real RESTful API
### Fixed
- Display context-type in properties tab again
- Store epochs (without exact date span)
- Add/Remove items from a list
- Display thumbnail after image upload
- Show tags (enabling _Show tags_ in filter tab didn't change anything)
- Search bar in tree column (Search results couldn't be expanded, thus children were not accessible)
- Deleting Context-Types
- Store/Retrieve double (datatype) attribute values
- Missing Attributes in Lumen Seeders
- Missing translation (Context datatype label)
- Missing dimension units in dropdown
### Removed/Deprecated

## 0.2 - Babylon

### Added
- Data Model Editor (Add/Delete Attributes, Add/Delete ContextTypes)
- Tree Search Bar
- Bibtex Importer
- Add Info Popup (left bottom corner)
- Guest Account and default Admin Account
### Changed
- New Design (Based on Material Design)
- Some other Style improvements (Geodata, Login Page, ...)
- Extended Photo Functionality
  - Delete Photos
  - Edit Photo Properties
  - Add Tags
  - Search By Tag, Camera Model and Date
  - Update to Leaflet 1.x
### Fixed
### Removed/Deprecated

## 0.1 - Atlantis

### Added
- Objects Tree View
- Object Property Editor
- Geomap
- Data Analysis (based on https://github.com/eScienceCenter/dbWebGen)
- Bibliography Manager
- Photo Manager: upload and linking
- User Manager: users, roles, permissions
