const de = {
    global: {
        save: 'Speichern',
        delete: 'Löschen',
        deactivate: 'Deaktivieren',
        reactivate: 'Reaktivieren',
        remove: 'Entfernen',
        cancel: 'Abbrechen',
        close: 'Schließen',
        add: 'Hinzufügen',
        edit: 'Editieren',
        edited: 'editiert',
        duplicate: 'Duplizieren',
        resort: 'Umsortieren',
        update: 'Aktualisieren',
        replace: 'Ersetzen',
        clear: 'Leeren',
        confirm: 'Ok',
        create: 'Anlegen',
        parse: 'Parsen',
        'select-all': 'Alle auswählen',
        'select-none': 'Alle abwählen',
        'delete-name': {
            title: '{name} löschen',
            desc: 'Willst du {name} wirklich löschen?'
        },
        'deactivate-name': {
            title: '{name} deaktivieren',
            desc: 'Willst du {name} wirklich deaktivieren?',
            info: 'Gewisse Einträge können nur/erst deaktiviert werden, da es sonst zu unvorhergesehenem Datenverlust kommen kann.'
        },
        'edit-name': {
            title: '{name} editieren'
        },
        'remove-name': {
            title: '{name} entfernen',
            desc: 'Willst du {name} wirklich entfernen?'
        },
        'unlink-name': {
            title: 'Verknüpfung entfernen - {name}',
            desc: 'Willst du die Verknüpfung von {file} und {ent} wirklich entfernen?'
        },
        'all-entities': 'Alle Entitäten',
        unlinked: 'Unverknüpft',
        unlink: 'Verknüpfung entfernen',
        link: 'Verknüpfen',
        'unlink-from': 'Verknüpfung mit {name} entfernen',
        'link-to': 'Mit {name} verknüpfen',
        'has-links': 'Hat keine Verknüpfungen | Hat eine Verknüpfung | Hat {cnt} Verknüpfungen',
        discard: {
            title: 'Ungespeicherte Änderungen',
            msg: 'Ungespeicherte Änderungen in <span class="font-weight-medium">{name}</span>. Willst du wirklich fortfahren und die Änderungen verwerfen?',
            confirm: 'Ja, Änderungen verwerfen',
            confirmpos: 'Nein, Speichern und fortfahren'
        },
        search: 'Suche...',
        login: 'Einloggen',
        'login-title': 'Anmelden',
        'login-subtitle': 'Willkommen bei Spacialist',
        download: 'Herunterladen',
        'download-name': '{name} herunterladen',
        upload: 'Hochladen',
        tools: {
            title: 'Werkzeuge',
            bibliography: 'Literatur',
            analysis: 'Datenanalyse',
            thesaurex: 'ThesauRex',
            dbwebgen: 'dbWebGen',
            external: 'Externe Werkzeuge',
            record: {
                start: 'Aufnahme starten',
                stop: 'Aufnahme beenden'
            }
        },
        settings: {
            title: 'Einstellungen',
            users: 'Benutzerverwaltung',
            roles: 'Rollenverwaltung',
            datamodel: 'Datenmodell-Editor',
            system: 'System-Einstellungen',
            about: 'Über'
        },
        user: {
            settings: 'Einstellungen',
            profile: 'Profil',
            logout: 'Ausloggen',
            info_title: 'Benutzerinformationen',
            member_since: 'Mitglied seit',
            deactivated_since: '<span class="font-weight-bold">Deaktiviert</span> seit {dt}',
            contact: 'Kontakt',
            avatar: 'Avatar',
            invalid_orcid: 'Die eigegebene ORCID ist ungültig',
        },
        select: {
            placehoder: 'Option auswählen',
            select: 'Drücke Enter zum hinzufügen',
            deselect: 'Drücke Enter zum entfernen'
        },
        attribute: 'Attribut',
        attributes: {
            string: 'Textfeld',
            stringf: 'Textbox',
            'string-sc': 'Einfachauswahl-Dropdown',
            'string-mc': 'Mehrfachauswahl-Dropdown',
            double: 'Gleitkommazahl',
            integer: 'Ganzzahl',
            boolean: 'Kontrollkästchen',
            percentage: 'Prozentangabe',
            entity: 'Entität',
            epoch: 'Zeitspanne & Epoche',
            timeperiod: 'Zeitspanne',
            date: 'Datum',
            dimension: 'Abmessungen (BxHxT)',
            list: 'Liste',
            geography: 'WKT (Well-Known-Binary)',
            table: 'Tabelle',
            sql: 'SQL-Abfrage',
            serial: 'Fortlaufende ID',
            'serial-info': `Alle Instanzen teilen diesen Identifier als Attribut. Benutze <code class="normal">%d</code> als Zähler.
            <br />
            <span class="font-weight-medium">Beispiel:</span>
            <br />
            <code class="normal">Fund_%d_Stein</code> würde zu Fund_1_Stein, Fund_2_Stein, &hellip; führen.
            <br />
            Um eine feste Breite (z.B. 3 für 002 anstatt 2) zu erzwingen, benutze <code class="normal">%03d</code>.`,
            iconclass: 'Iconclass'
        },
        active: 'Aktiviert',
        activity: 'Aktivitäten',
        action: 'Aktion',
        users: 'Benutzer',
        timespan: '@:global.attributes.timeperiod',
        visible: 'Sichtbar',
        invisible: 'Unsichtbar',
        opacity: 'Deckkraft',
        transparency: 'Transparenz',
        text: 'Text',
        font: 'Schrift',
        mode: 'Modus',
        size: 'Größe',
        duration: 'Dauer',
        color: 'Farbe',
        format: 'Format',
        version: 'Version',
        label: 'Beschriftung',
        url: 'URL',
        name: 'Name',
        nickname: 'Spitzname',
        'display-name': 'Anzeigename',
        email: 'E-Mail-Adresse',
        email_or_nick: 'E-Mail-Adresse oder Name',
        phonenumber: 'Telefonnummer',
        password: 'Passwort',
        'remember-me': 'Eingeloggt bleiben',
        'orcid': 'ORCID',
        description: 'Beschreibung',
        roles: 'Rollen',
        permissions: 'Berechtigungen',
        added_at: 'Hinzugefügt',
        created_at: 'Erstellt',
        updated_at: 'Aktualisert',
        deactivated_at: 'Deaktiviert',
        timestamp: 'Zeitstempel',
        options: 'Optionen',
        reply_to: 'Antworten',
        replying_to: 'Antwort an {name}',
        type: 'Typ',
        'root-attribute': 'Eltern-Attribut',
        'root-attribute-toggle': 'Wert eines vorhandenen Attributs als Eltern-Element verwenden',
        'root-element': 'Eltern-Element',
        recursive: 'Alle Kind-Elemente (Rekursiv)',
        content: 'Inhalt',
        column: 'Spalte | Spalten',
        'geometry-type': 'Geometrietyp',
        'depends-on': 'Hängt ab von',
        preference: 'Einstellung',
        value: 'Wert',
        'allow-override': 'Überschreibbar?',
        tag: 'Schlagwort | Schlagworte',
        set: 'Setzen',
        'has-tags': 'Hat keine Schlagworte | Hat ein Schlagwort | Hat {cnt} Schlagworte',
        'from-subentity': 'Gehört zu einer Sub-Entität',
        comments: {
            deleted_info: 'Kommentar gelöscht',
            empty_list: 'Bisher keine Kommentare hinzugefügt.',
            hide: 'Kommentare ausblenden',
            show: 'Kommentare anzeigen',
            submit: 'Kommtentar abschicken',
            text_placeholder: 'Kommentar eingeben',
            hide_reply: '<span class="font-weight-bold">Eine</span> Antwort ausblenden | <span class="font-weight-bold">{cnt}</span> Antworten ausblenden',
            show_reply: '<span class="font-weight-bold">Eine</span> Antwort anzeigen | <span class="font-weight-bold">{cnt}</span> Antworten anzeigen',
            fetching: 'Lade Kommentare&hellip;',
            fetching_failed: 'Laden der Kommentare fehlgeschlagen!',
            retry_failed: 'Wiederholen',
        },
        notifications: {
            title: 'Benachrichtigungen',
            count: 'Benachrichtigungen ({cnt})',
            mark_all_as_read: 'Alle als gelesen markieren',
            delete_all: 'Alle löschen',
            empty_list: 'Keine Benachrichtigungen',
            view_all: 'Alle anzeigen',
            tab_all: 'Alle',
            tab_unread: 'Ungelesen',
            tab_read: 'Gelesen',
            tab_system: 'System-Benachrichtigungen',
            tab_default_empty_list: 'Du bist auf dem neusten Stand. Keine Benachrichtigungen!',
            tab_unread_empty_list: 'Du bist auf dem neusten Stand. Keine neuen Benachrichtigungen!',
            tab_system_empty_list: 'Keine System-Benachrichtigungen. Du musst nichts unternehmen!',
            body: {
                title: 'Neue Benachrichtigung',
                type: {
                    system: 'System-Nachricht'
                },
                user_left_comment_on: 'hat bei <span class="font-weight-bold">{name}</span> einen Kommentar hinterlassen.',
                reply: 'Antworten',
                mention_info: 'Schreibe deine Antwort. Verwende @nickname um andere Nutzer zu erwähnen.',
                reply_sent: 'Antwort abgeschickt',
                reply_to_user: '<span class="font-weight-bold">{name}</span> antworten',
                reply_to_chat: 'Als Kommentar antworten',
            }
        }
    },
    main: {
        entity: {
            title: 'Entität | Entitäten',
            count: 'Keine Top-Level Entitäten | Eine Top-Level Entität | {cnt} Top-Level Entitäten',
            toasts: {
                updated: {
                    title: 'Entität aktualisiert',
                    msg: 'Daten von {name} wurden erfolgreich aktualisiert.'
                },
                deleted: {
                    title: 'Entität gelöscht',
                    msg: '{name} wurde erfolgreich gelöscht.'
                }
            },
            tree: {
                add: 'Neue Top-Level Entität hinzufügen',
                sorts: {
                    asc: {
                        rank: 'Rang - Aufsteigend (Drag & Drop)',
                        name: 'Name - Aufsteigend',
                        children: 'Anzahl Unter-Entitäten - Aufsteigend',
                        type: 'Entitätstyp - Aufsteigend'
                    },
                    desc: {
                        rank: 'Rang - Absteigend',
                        name: 'Name - Absteigend',
                        children: 'Anzahl Unter-Entitäten - Absteigend',
                        type: 'Entitätstyp - Absteigend'
                    }
                }
            },
            tabs: {
                attributes: 'Attribute',
                comments: 'Kommentare',
            },
            modals: {
                add: {
                    title: 'Neue Entität'
                },
                edit: {
                    title: 'Entität editieren - {name}'
                },
                screencast: {
                    title: 'Screencast speichern',
                    info: 'Screencast erfolgreich aufgenommen. Was möchtest du damit tun?',
                    actions: {
                        local: {
                            button: 'Lokal speichern'
                        },
                        server: {
                            button: 'In Spacialist speichern'
                        }
                    }
                }
            },
            menu: {
                add: 'Neue Entität hinzufügen',

            },
            references: {
                title: 'Quellenangaben',
                empty: 'Keine Quellen gefunden',
                certainty: 'Sicherheit',
                certaintyc: 'Kommentar',
                certaintyu: 'Sicherheit aktualisieren',
                bibliography: {
                    title: 'Quellen',
                    add: 'Neue Quellen hinzufügen',
                    comment: 'Kommentar',
                    'add-button': 'Quelle hinzufügen'
                },
                toasts: {
                    'updated-certainty': {
                        title: 'Sicherheit aktualisiert',
                        msg: 'Sicherheit von {name} erfolgreich auf {i}% gesetzt.'
                    }
                }
            },
            attributes: {
                'open-map': 'Karte öffnen',
                'add-wkt': 'WKT hinzufügen',
                'set-location': 'Position setzen',
                BC: 'v. Chr.',
                bc: '@:main.entity.attributes.BC',
                AD: 'n. Chr.',
                ad: '@:main.entity.attributes.AD',
                hidden: 'keine Attribute ausgeblendet | ein Attribut ausgeblendet | {cnt} Attribute ausgeblendet',
                iconclass: {
                    doesnt_exist: 'Diese Iconclass existiert nicht'
                },
                table: {
                    chart: {
                        number_sets: 'Die letzten {cnt} Datensätze',
                        include_in: 'Verwenden',
                        use_difference: 'Differenz verwenden',
                        use_as_label: 'Als Beschriftung verwenden'
                    }
                }
            }
        },
        user: {
            'add-button': 'Neuen Benutzer hinzufügen',
            active_users: 'Aktive Benutzer',
            deactivated_users: 'Deaktivierte Benutzer',
            empty_list: 'Benutzerliste ist leer',
            toasts: {
                updated: {
                    title: 'Benutzer aktualisiert',
                    msg: '{name} wurde erfolgreich aktualisiert.'
                }
            },
            modal: {
                new: {
                    title: 'Neuer Benutzer'
                }
            },
            'add-role-placeholder': 'Rollen hinzufügen'
        },
        role: {
            'add-button': 'Neue Rolle hinzufügen',
            toasts: {
                updated: {
                    title: 'Rolle aktualisiert',
                    msg: '{name} wurde erfolgreich aktualisiert.'
                }
            },
            modal: {
                new: {
                    title: 'Neue Rolle'
                }
            },
            'add-permission-placeholder': 'Berechtigungen hinzufügen'
        },
        activity: {
            title: '@:global.activity',
            title_project: 'Projekt-Aktivität | Projekt-Aktivitäten',
            title_user: 'Deine Aktivität | Deine Aktivitäten',
            nr_of_entries: 'Keine Einträge | 1 Eintrag | {cnt} Einträge',
            no_results: 'Keine Ergebnisse gefunden.',
            rawdata: 'Rohdaten',
            apply_filter: 'Filter anwenden',
            search_in_raw_data: 'In Rohdaten suchen',
            hide_filter_panel: 'Filter verstecken',
            toggle_raw_data: 'Anzeige der Rohdaten umschalten',
            toggle_pretty_print: 'Darstellung umschalten',
            fetch_next_entries: 'Weitere Einträge laden',
        },
        datamodel: {
            toasts: {
                'updated-type': {
                    title: 'Entitätstyp aktualisiert',
                    msg: '{name} erfolgreich aktualisiert.'
                },
                'added-attribute': {
                    title: 'Attribut hinzugefügt',
                    msg: '{name} erfolgreich zu {etName} hinzugefügt.'
                },
                attribute_deleted: {
                    title: 'Attribut gelöscht',
                    msg: '{name} erfolgreich gelöscht.'
                }
            },
            attribute: {
                title: 'Verfügbare Attribute',
                'add-button': 'Attribut hinzufügen',
                modal: {
                    new: {
                        title: 'Neues Attribut'
                    },
                    delete: {
                        alert: 'Beachte: Wenn du {name} löschst, wird ein Attributwert ebenfalls gelöscht. | Beachte: Wenn du {name} löschst, werden {cnt} Attributwerte ebenfalls gelöscht.'
                    }
                }
            },
            entity: {
                title: 'Verfügbare Entitätstypen',
                'add-button': 'Entitätstyp hinzufügen',
                modal: {
                    new: {
                        title: 'Neuer Entitätstyp'
                    },
                    delete: {
                        alert: 'Beachte: Wenn du {name} löschst, wird eine Entität ebenfalls gelöscht. | Beachte: Wenn du {name} löschst, werden {cnt} Entitäten ebenfalls gelöscht.'
                    }
                }
            },
            detail: {
                properties: {
                    title: 'Eigenschaften',
                    'top-level': 'Top-Level Entitätstyp?',
                    'sub-types': 'Erlaubte Unter-Entitätstypen'
                },
                attribute: {
                    title: 'Hinzugefügte Attribute',
                    alert: 'Beachte: Wenn du {name} entfernst, wird ein weiterer Eintrag unter {refname} ebenfalls gelöscht. | Beachte: Wenn du {name} entfernst, werden {cnt} weitere Einträge unter {refname} ebenfalls gelöscht.'
                }
            }
        },
        preference: {
            toasts: {
                updated: {
                    title: 'Einstellung aktualisiert',
                    msg: '{name} wurde erfolgreich aktualisiert.'
                }
            },
            info: {
                password_reset_link: 'Wenn diese Option aktiviert wird, müssen die <code>MAIL_*</code>-Werte in der <code>.env</code>-Datei gesetzt werden. <a href="https://github.com/eScienceCenter/Spacialist/blob/master/INSTALL.md#send-mails" target="_blank">Weitere Informationen</a>.',
                columns: 'Die Summe der Werte der Spalten darf 12 nicht überschreiten.'
            },
            key: {
                language: 'Sprache',
                password_reset_link: 'Passwort per Link zurücksetzen',
                columns: {
                    title: 'Spalten Hauptansicht',
                    left: 'Linke Spalte',
                    center: 'Mittlere Spalte',
                    right: 'Rechte Spalte',
                },
                tooltips: 'Infos anzeigen',
                'tag-root': 'Thesaurus-URI für Schlagworte',
                extensions: 'Geladene Erweiterungen',
                'link-thesaurex': 'Link zu ThesauRex anzeigen',
                project: {
                    name: 'Projektname',
                    maintainer: 'Verantwortlicher',
                    public: 'Öffentlich verfügbar'
                },
                map: {
                    projection: 'Kartenprojektion',
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
            title: 'Über Spacialist',
            desc: 'Spacialist wird vom Ministerium für Wissenschaft, Forschung und Kunst, Baden-Württemberg im Rahmen des "E-Science"-Programms gefördert.',
            release: {
                name: 'Name der Veröffentlichung',
                time: 'Datum der Veröffentlichung',
                'full-name': 'Vollständiger Name'
            },
            'build-info': 'Mit <i class="fab fa-fw fa-laravel"></i> & <i class="fab fa-fw fa-vuejs"></i> erstellt!',
            contributor: 'Mitwirkender | Mitwirkende'
        },
        bibliography: {
            modal: {
                new: {
                    title: 'Neuer Eintrag',
                    'bibtex-code': 'BibTeX-Code'
                },
                edit: {
                    title: 'Eintrag editieren'
                },
                delete: {
                    title: 'Eintrag löschen',
                    alert: 'Beachte: Wenn du {name} löschst, wird eine Quellenangabe ebenfalls gelöscht. | Beachte: Wenn du {name} löschst, werden {cnt} Quellenangaben ebenfalls gelöscht.',
                }
            },
            add: 'Neuen Literatur-Eintrag anlegen',
            import: 'BibTeX-Datei importieren',
            export: 'BibTeX-Datei exportieren',
            'show-all-fields': 'Alle Felder anzeigen',
            column: {
                'cite-key': 'Zitations-Schlüssel',
                author: 'Autor',
                editor: 'Herausgeber',
                title: 'Titel',
                journal: 'Zeitschrift',
                year: 'Jahr',
                month: 'Monat',
                pages: 'Seiten',
                volume: 'Band',
                number: 'Ausgabe',
                chapter: 'Kapitel',
                edition: 'Edition',
                series: 'Reihe',
                booktitle: 'Buchtitel',
                publisher: 'Verlag',
                address: 'Ort',
                note: 'Notiz',
                misc: 'Diverse',
                howpublished: 'Verweis',
                institution: 'Institution',
                organization: 'Organisation',
                school: 'Schule'
            }
        },
        map: {
            'init-error': 'Mehrere init-*-Attribute gesetzt. Es darf nur eines gesetzt sein.',
            layer: 'Ebene | Ebenen',
            baselayer: 'Basis-Ebene | Basis-Ebenen',
            overlay:'Overlay | Overlays',
            'entity-layers': 'Entitäts-Ebenen',
            'geometry-name': 'Geometrie #{id}',
            'coords-in-epsg': 'Koordinaten in EPSG:{epsg}',
            length: 'Länge',
            area: 'Fläche',
            draw: {
                point: {
                    desc: 'Punkt anlegen',
                },
                linestring: {
                    desc: 'Linie anlegen',
                },
                polygon: {
                    desc: 'Polygon anlegen',
                },
                modify: {
                    desc: 'Geometrien bearbeiten',
                    'pos-desc': 'Änderungen speichern',
                    'neg-desc': 'Änderungen verwerfen'
                },
                delete: {
                    desc: 'Geometrien löschen',
                    'pos-desc': 'Löschen bestätigen',
                    'neg-desc': 'Löschen verwerfen'
                },
                measure: {
                    desc: 'Maßband umschalten'
                }
            }
        }
    }
};

export default de;
