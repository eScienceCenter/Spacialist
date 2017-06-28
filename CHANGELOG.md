# Changelog
All notable changes to this project will be documented in this file.

## Unreleased - Cannae

### Added
- Drag & Drop Support for Element Tree
- More Datatypes (Integer, Geography, Slider)
- Dynamic UI (The three columns on the main view can be shrinked/extended, enable Edit Mode in the Settings Dropdown menu)
### Changed
- Photo Viewer => File Viewer
  - PDFs (pdf.js)
  - Text documents (+ text highlighting using highlightjs)
  - 3D files (`.dae` and `.obj`/`.mtl`, using three.js)
  - Markdown files (marked.js)
  - Audio/Video files
- Remove input field placeholders
- Layer Control
  - Add layers for all existing contexttypes + one layer for unlinked geodata objects
  - Contexttype layers are restricted to a type (either MultiPoint, MultiLinestring or Multipolygon)
  - Geodata snapping
  - Add Bing and Google Maps as Tile-Provider
  - Layer Editor
    - Change color
    - Enable/Disable layers (overlays, on startup)
    - Set opacity
    - Set default baselayer
### Fixed
- Display context-type in properties tab again
- Store epochs (without exact date span)
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