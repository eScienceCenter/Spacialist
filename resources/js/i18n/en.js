const en = {
    global: {
        save: 'Save',
        delete: 'Delete',
        remove: 'Remove',
        cancel: 'Cancel',
        close: 'Close',
        add: 'Add',
        edit: 'Edit',
        duplicate: 'Duplicate',
        resort: 'Re-sort',
        update: 'Update',
        replace: 'Replace',
        clear: 'Clear',
        confirm: 'OK',
        create: 'Create',
        parse: 'Parse',
        'select-all': 'Select All',
        'select-none': 'Deselect All',
        'delete-name': {
            title: 'Delete {name}',
            desc: 'Do you really want to delete {name}?'
        },
        'edit-name': {
            title: 'Edit {name}'
        },
        'remove-name': {
            title: 'Remove {name}',
            desc: 'Do you really want to remove {name}?'
        },
        'unlink-name': {
            title: 'Remove Link - {name}',
            desc: 'Do you really want to unlink {file} from {ent}?'
        },
        'all-entities': 'All Entities',
        unlinked: 'Unlinked',
        unlink: 'Unlink',
        link: 'Link',
        'unlink-from': 'Unlink from {name}',
        'link-to': 'Link to {name}',
        'has-links': 'Has no links | Has one link | Has {cnt} links',
        discard: {
            title: 'Unsaved Changes',
            msg: 'Unsaved changes in <span class="font-weight-medium">{name}</span>. Do you really want to continue and discard changes?',
            confirm: 'Yes, discard changes',
            confirmpos: 'No, save and continue'
        },
        search: 'Search...',
        login: 'Login',
        'login-title': 'Login',
        'login-subtitle': 'Welcome to Spacialist',
        download: 'Download',
        'download-name': 'Download {name}',
        upload: 'Upload',
        tools: {
            title: 'Tools',
            bibliography: 'Bibliography',
            analysis: 'Data Analysis',
            thesaurex: 'ThesauRex',
            dbwebgen: 'dbWebGen',
            external: 'External Tools',
            record: {
                start: 'Start Recording',
                stop: 'Stop Recording'
            }
        },
        settings: {
            title: 'Settings',
            users: 'User Management',
            roles: 'Role Management',
            datamodel: 'Data-Model-Editor',
            system: 'System Preferences',
            about: 'About'
        },
        user: {
            settings: 'Preferences',
            logout: 'Logout'
        },
        select: {
            placehoder: 'Select option',
            select: 'Press enter to select',
            deselect: 'Press enter to remove'
        },
        attribute: 'Attribute',
        attributes: {
            string: 'Textfield',
            stringf: 'Textbox',
            'string-sc': 'Single Choice Dropdown',
            'string-mc': 'Multiple Choice Dropdown',
            double: 'Numeric Input (Floating Point)',
            integer: 'Numeric Input (Integer)',
            boolean: 'Checkbox',
            percentage: 'Percentage',
            entity: 'Entity',
            epoch: 'Time Period and Epoch',
            timeperiod: 'Time Period',
            date: 'Date',
            dimension: 'Dimensions (BxHxT)',
            list: 'List',
            geography: 'WKT (Well-Known-Binary)',
            table: 'Table',
            sql: 'SQL-Query',
            serial: 'Serial (Auto-incrementing ID)',
            'serial-info': `All instances share this identifier as attribute. Add <code class="normal">%d</code> as counter.
            <br />
            <span class="font-weight-medium">Example:</span>
            <br />
            <code class="normal">Find_%d_Stone</code> would create Find_1_Stone, Find_2_Stone, &hellip;
            <br />
            To add a fixed width (e.g. 3 for 002 instead of 2), you can use <code class="normal">%03d</code>.`,
            iconclass: 'Iconclass'
        },
        active: 'Active',
        visible: 'Visible',
        invisible: 'Invisible',
        opacity: 'Opacity',
        transparency: 'Transparency',
        text: 'Text',
        font: 'Font',
        mode: 'Mode',
        size: 'Size',
        duration: 'Duration',
        color: 'Color',
        format: 'Format',
        version: 'Version',
        label: 'Label',
        url: 'URL',
        name: 'Name',
        nickname: 'Nickname',
        'display-name': 'Displayname',
        email: 'E-Mail Address',
        email_or_nick: 'E-Mail Address or Nickname',
        password: 'Password',
        'remember-me': 'Remember me',
        description: 'Description',
        moderation: 'Moderated',
        moderation_description: 'Moderated roles need a user with privileged role to accept/deny their changes',
        roles: 'Roles',
        permissions: 'Permissions',
        'added-at': 'Added',
        'created-at': 'Created',
        'updated-at': 'Updated',
        options: 'Options',
        type: 'Type',
        'root-attribute': 'Parent-Attribute',
        'root-attribute-toggle': 'Use value of an existing attribute as Parent-Element',
        'root-element': 'Parent-Element',
        recursive: 'All descendants (recursive)',
        content: 'Content',
        column: 'Column | Columns',
        'geometry-type': 'Geometry-Type',
        'depends-on': 'Depends on',
        preference: 'Preference',
        value: 'Value',
        'allow-override': 'Allow Override?',
        tag: 'Tag | Tags',
        set: 'Set',
        'has-tags': 'Has no tags | Has one tag | Has {cnt} tags',
        'from-subentity': 'Is from Sub-Entity'
    },
    main: {
        entity: {
            title: 'Entities',
            count: 'No Top-Level-Entities | One Top-Level-Entity | {cnt} Top-Level-Entities',
            toasts: {
                updated: {
                    title: 'Entity updated',
                    msg: 'Data of {name} successfully updated.'
                },
                deleted: {
                    title: 'Entität gelöscht',
                    msg: '{name} wurde erfolgreich gelöscht.'
                }
            },
            tree: {
                add: 'Add new Top-Level-Entity',
                sorts: {
                    asc: {
                        rank: 'Rank - Ascending (Drag & Drop)',
                        name: 'Name - Ascending',
                        children: 'Count Sub-Entities - Ascending',
                        type: 'Entity-Type - Ascending'
                    },
                    desc: {
                        rank: 'Rank - Descending',
                        name: 'Name - Descending',
                        children: 'Count Sub-Entities - Descending',
                        type: 'Entity-Type - Descending'
                    }
                }
            },
            modals: {
                add: {
                    title: 'New Entity'
                },
                edit: {
                    title: 'Edit Entity - {name}'
                },
                screencast: {
                    title: 'Save Screencast',
                    info: 'Screencast recorded. What do you want to do with it?',
                    actions: {
                        local: {
                            button: 'Store local'
                        },
                        server: {
                            button: 'Store in Spacialist'
                        }
                    }
                }
            },
            menu: {
                add: 'Add new Entity',

            },
            references: {
                title: 'References',
                empty: 'No References found',
                certainty: 'Certainty',
                certaintyc: 'Comment',
                certaintyu: 'Update Certainty',
                bibliography: {
                    title: 'Literature',
                    add: 'Add new Reference',
                    comment: 'Comment',
                    'add-button': 'Add Reference'
                },
                toasts: {
                    'updated-certainty': {
                        title: 'Certainty updated',
                        msg: 'Certainty of {name} successfully set to {i}% ({desc}).'
                    }
                }
            },
            attributes: {
                'open-map': 'Open Map',
                'add-wkt': 'Add WKT',
                'set-location': 'Set Location',
                BC: 'BC',
                bc: '@:main.entity.attributes.BC',
                AD: 'AD',
                ad: '@:main.entity.attributes.AD',
                hidden: 'no attributes hidden | one attribute hidden | {cnt} attributes hidden',
                iconclass: {
                    doesnt_exist: 'This iconclass does not exist'
                }
            }
        },
        user: {
            'add-button': 'Add new User',
            toasts: {
                updated: {
                    title: 'User updated',
                    msg: '{name} successfully updated.'
                }
            },
            modal: {
                new: {
                    title: 'New User'
                }
            },
            'add-role-placeholder': 'Add roles'
        },
        role: {
            'add-button': 'Add new Role',
            toasts: {
                updated: {
                    title: 'Role updated',
                    msg: '{name} successfully updated.'
                }
            },
            modal: {
                new: {
                    title: 'New Role'
                }
            },
            'add-permission-placeholder': 'Add permissions',
            moderation: {
                locked_state_info: 'This attribute is locked, because it\'s value is still in moderation and not accepted yet.',
                view_modified_data: 'View modified data',
                view_original_data: 'View original data',
                accept_changes: 'Accept changes',
                deny_changes: 'Deny changes',
                deleted_by: '<i class="fas fa-fw fa-trash"></i> Deleted by <span class="font-weight-bold"><i class="fas fa-fw fa-user-edit"></i> {name}</span> on {ts}',
                modified_by: '<i class="fas fa-fw fa-edit"></i> Edited by <span class="font-weight-bold"><i class="fas fa-fw fa-user-edit"></i> {name}</span> on {ts}',
            }
        },
        datamodel: {
            toasts: {
                'updated-type': {
                    title: 'Entity-Type updated',
                    msg: '{name} successfully updated.'
                },
                'added-attribute': {
                    title: 'Attribute added',
                    msg: '{name} successfully added to {etName}.'
                },
                attribute_deleted: {
                    title: 'Attribute deleted',
                    msg: '{name} successfully deleted.'
                }
            },
            attribute: {
                title: 'Available Attributes',
                'add-button': 'Add Attribute',
                modal: {
                    new: {
                        title: 'New Attribute'
                    },
                    delete: {
                        alert: 'Note: If you delete {name}, one attribute value is deleted, too. | Note: If you delete {name}, {cnt} attribute values are deleted, too.'
                    }
                }
            },
            entity: {
                title: 'Available Entity-Types',
                'add-button': 'Add Entity-Type',
                modal: {
                    new: {
                        title: 'New Entity-Type'
                    },
                    delete: {
                        alert: 'Note: If you delete {name}, one entity is deleted, too. | Note: If you delete {name}, {cnt} entities are deleted, too.'
                    }
                }
            },
            detail: {
                properties: {
                    title: 'Properties',
                    'top-level': 'Top-Level-Entity-Type?',
                    'sub-types': 'Allowed Sub-Entity-Types'
                },
                attribute: {
                    title: 'Added Attributes',
                    alert: 'Note: If you remove {name}, one entry at {refname} is deleted, too. | Note: If you remove {name}, {cnt} entries at {refname} are deleted, too.'
                }
            }
        },
        preference: {
            toasts: {
                updated: {
                    title: 'Preference updated',
                    msg: '{name} successfully updated.'
                }
            },
            info: {
                password_reset_link: 'If this option is enabled, you have to set the <code>MAIL_*</code> settings in your <code>.env</code> file. <a href="https://github.com/eScienceCenter/Spacialist/blob/master/INSTALL.md#send-mails" target="_blank">Further informations</a>.',
                columns: 'The sum of the column values must not be higher than 12.'
            },
            key: {
                language: 'Language',
                password_reset_link: 'Enable Reset Password Link',
                columns: {
                    title: 'Columns in Main View',
                    left: 'Left Column',
                    center: 'Center Column',
                    right: 'Right Column',
                },
                tooltips: 'Show Tooltips',
                'tag-root': 'Thesaurus-URI for Tags',
                extensions: 'Loaded Extensions',
                'link-thesaurex': 'Show link to ThesauRex',
                project: {
                    name: 'Projectname',
                    maintainer: 'Maintainer',
                    public: 'Public accessible'
                },
                map: {
                    projection: 'Map Projection',
                    epsg: 'EPSG-Code'
                }
            },
            labels: {
                prefs: {
                    'gui-language': '@:main.preference.key.language',
                    'columns': '@:main.preference.key.columns.title',
                    'show-tooltips': '@:main.preference.key.tooltips',
                    'tag-root': '@:main.preference.key.tag-root',
                    'load-extensions': '@:main.preference.key.extensions',
                    'link-to-thesaurex': '@:main.preference.key.link-thesaurex',
                    'project-name': '@:main.preference.key.project.name',
                    'project-maintainer': '@:main.preference.key.project.maintainer',
                    'map-projection': '@:main.preference.key.map.projection',
                }
            }
        },
        about: {
            title: 'About Spacialist',
            desc: 'Development of Spacialist is co-funded by the Ministry of Science, Research and the Arts Baden-Württemberg in the "E-Science" funding programme.',
            release: {
                name: 'Release Name',
                time: 'Release Date',
                'full-name': 'Full Name'
            },
            'build-info': 'Built with <i class="fab fa-fw fa-laravel"></i> & <i class="fab fa-fw fa-vuejs"></i>!',
            contributor: 'Contributor | Contributors'
        },
        bibliography: {
            modal: {
                new: {
                    title: 'New Entry',
                    'bibtex-code': 'BibTeX-Code'
                },
                edit: {
                    title: 'Edit entry'
                },
                delete: {
                    title: 'Delete entry',
                    alert: 'Note: If you delete {name}, one reference is deleted, too. | Note: If you delete {name}, {cnt} references are deleted, too.',
                }
            },
            add: 'Add new Bibliography-Entry',
            import: 'Import BibTeX-File',
            export: 'Export BibTeX-File',
            'show-all-fields': 'Show all fields',
            column: {
                'cite-key': 'Citation-Key',
                author: 'Author',
                editor: 'Editor',
                title: 'Title',
                journal: 'Journal',
                year: 'Year',
                month: 'Month',
                pages: 'Pages',
                volume: 'Volumne',
                number: 'Number',
                chapter: 'Chapter',
                edition: 'Edition',
                series: 'Series',
                booktitle: 'Book title',
                publisher: 'Publisher',
                address: 'Place',
                note: 'Note',
                misc: 'Misc',
                howpublished: 'How Published',
                institution: 'Institution',
                organization: 'Organization',
                school: 'School'
            }
        },
        map: {
            'init-error': 'Several init-*-Attributes are provided. Only one is allowed.',
            layer: 'Layer | Layers',
            baselayer: 'Baselayer | Baselayers',
            overlay:'Overlay | Overlays',
            'entity-layers': 'Entity-Layer',
            'geometry-name': 'Geometry #{id}',
            'coords-in-epsg': 'Coordinates in EPSG:{epsg}',
            length: 'Length',
            area: 'Area',
            draw: {
                point: {
                    desc: 'Draw Point',
                },
                linestring: {
                    desc: 'Draw Line',
                },
                polygon: {
                    desc: 'Draw Polygon',
                },
                modify: {
                    desc: 'Modify geometries',
                    'pos-desc': 'Save modifications',
                    'neg-desc': 'Discard modifications'
                },
                delete: {
                    desc: 'Delete geometries',
                    'pos-desc': 'Confirm delete',
                    'neg-desc': 'Discard delete'
                },
                measure: {
                    desc: 'Toggle measuring tape'
                }
            }
        }
    }
};

export default en;
