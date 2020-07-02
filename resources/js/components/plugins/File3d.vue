<template>
    <div class="h-100">
        <transition name="fade">
            <div class="progress mb-2" style="height: 1px;" v-if="progress<100">
                <div class="progress-bar" role="progressbar" :style="{width: progress+'%'}" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100">
                    <span class="sr-only">{{progress}}</span>
                </div>
            </div>
        </transition>
        <div :id="containerId" class="w-100 h-100">
            <div id="file-controls" class="position-relative">
                <button type="button" class="btn btn-outline-info position-absolute m-2" v-if="entity.id" @click="loadAllSubModels" style="left: 0;">
                    {{ $t('plugins.files.modal.detail.threed.load-sub-models') }}
                </button>
                <button type="button" class="btn btn-outline-info position-absolute m-2" v-if="fullscreenHandler" @click="toggleFullscreen" style="right: 0;">
                    <i class="fas fa-fw fa-expand"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import {
        AmbientLight,
        AnimationMixer,
        BoxBufferGeometry,
        Color,
        Clock,
        DirectionalLight,
        DoubleSide,
        Geometry,
        GridHelper,
        Group,
        HemisphereLight,
        IcosahedronBufferGeometry,
        Line,
        MathUtils as TMath,
        LOD,
        Matrix4,
        Mesh,
        MeshPhongMaterial,
        PCFSoftShadowMap,
        PerspectiveCamera,
        Raycaster,
        Scene,
        SpotLight,
        TextureLoader,
        Vector2,
        Vector3,
        WebGLRenderer,
    } from 'three';
    import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js';
    import { TransformControls } from 'three/examples/jsm/controls/TransformControls.js';
    import { ColladaLoader } from 'three/examples/jsm/loaders/ColladaLoader.js';
    import { FBXLoader } from 'three/examples/jsm/loaders/FBXLoader.js';
	import { GCodeLoader } from 'three/examples/jsm/loaders/GCodeLoader.js';
    import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
    import { MTLLoader } from 'three/examples/jsm/loaders/MTLLoader.js';
    import { OBJLoader2 } from 'three/examples/jsm/loaders/OBJLoader2.js';
    import { MtlObjBridge } from 'three/examples/jsm/loaders/obj2/bridge/MtlObjBridge.js';
    import { PDBLoader } from 'three/examples/jsm/loaders/PDBLoader.js';
    import { STLLoader } from 'three/examples/jsm/loaders/STLLoader.js';
    import { CSS2DRenderer, CSS2DObject } from 'three/examples/jsm/renderers/CSS2DRenderer.js';
    import * as dat from 'three/examples/jsm/libs/dat.gui.module.js';
    import { VRButton } from 'three/examples/jsm/webxr/VRButton.js';

    export default {
        props: {
            file: {
                required: true,
                type: Object
            },
            entity: {
                required: false,
                type: Object
            },
            fullscreenHandler: {
                required: false,
                type: Object
            }
        },
        mounted() {
            this.startup();
        },
        destroyed() {
            window.removeEventListener('resize', this.onWindowResize, false);
            this.renderer.domElement.removeEventListener('mousedown', this.onMouseDown, false);
            // VR Events
            if(this.grabController) {
                this.grabController.removeEventListener('selectstart', this.onGrabDown);
                this.grabController.removeEventListener('selectend', this.onGrabUp);
                this.grabController.removeEventListener('thumbpadup', this.dimWorldLight);
                // this.grabController.removeEventListener('axischanged', this.recognizeTouch);
            }
            if(this.flashlightController) {
                this.flashlightController.removeEventListener('selectstart', this.onLightOn);
                this.flashlightController.removeEventListener('selectend', this.onLightOff);
                this.flashlightController.removeEventListener('thumbpadup', this.dimFlashLight);
            }

            window.removeEventListener('vrdisplaypresentchange', this.vrDisplayStateChanged, false);

            for(let i=this.scene.children.length-1; i>=0; i--) {
                let obj = this.scene.children[i];
                if(obj.geometry) obj.geometry.dispose();
                if(obj.material) obj.material.dispose();
                this.scene.remove(obj);
            }
            this.renderer.forceContextLoss();
            this.renderer.dispose();
            this.renderer = null;
            this.scene = null;
            if(this.labelRenderer) {
                this.labelRenderer.forceContextLoss();
                this.labelRenderer.dispose();
                this.labelRenderer = null;
            }
        },
        methods: {
            setScale() {
                if(!this.activeMesh) return;
                const sx = this.guiConfig.scaleX;
                const sy = this.guiConfig.scaleY;
                const sz = this.guiConfig.scaleZ;
                this.activeMesh.scale.set(sx, sy, sz);
            },
            setPosition() {
                if(!this.activeMesh) return;
                const px = this.guiConfig.positionX;
                const py = this.guiConfig.positionY;
                const pz = this.guiConfig.positionZ;
                this.activeMesh.position.set(px, py, pz);
            },
            getFileType: function(file) {
                switch(file.mime_type) {
                    case 'model/vnd.collada+xml':
                        return 'dae';
                    case 'model/gltf-binary':
                    case 'model/gltf+json':
                        return 'gltf';
                    case 'chemical/x-pdb':
                        return 'pdb';
                    case 'application/sla':
                        return 'stl';
                }
                let extension = this.getFileExtension(file.name);
                switch(extension) {
                    case 'dae':
                        return 'dae';
                    case 'gltf':
                        return 'gltf';
                    case 'pdb':
                        return 'pdb';
                    case 'obj':
                        return 'obj';
                    case 'fbx':
                        return 'fbx';
                    case 'stl':
                        return 'stl';
                    case 'gcode':
                        return 'gcode';
                    default:
                        return undefined;
                }
            },
            getFileExtension: function(filename) {
                return filename.substr(filename.lastIndexOf('.')+1);
            },
            startup: function() {
                this.init();
                this.loadModel(this.file);
                this.animate();
            },
            init: function() {
                this.gui = new dat.GUI({
                    autoPlace: false,
                    hideable: true,
                    closed: true
                });
                this.guiConfig = {
                    scaleX: this.scale,
                    scaleY: this.scale,
                    scaleZ: this.scale,
                    positionX: 0,
                    positionY: 0,
                    positionZ: 0
                };
                this.guiCtrl['scaleX'] = this.gui.add(this.guiConfig, 'scaleX', 0.01, 100, 0.01).onChange(this.setScale);
                this.guiCtrl['scaleY'] = this.gui.add(this.guiConfig, 'scaleY', 0.01, 100, 0.01).onChange(this.setScale);
                this.guiCtrl['scaleZ'] = this.gui.add(this.guiConfig, 'scaleZ', 0.01, 100, 0.01).onChange(this.setScale);
                this.guiCtrl['positionX'] = this.gui.add(this.guiConfig, 'positionX', -100, 100, 0.01).onChange(this.setPosition);
                this.guiCtrl['positionY'] = this.gui.add(this.guiConfig, 'positionY', -100, 100, 0.01).onChange(this.setPosition);
                this.guiCtrl['positionZ'] = this.gui.add(this.guiConfig, 'positionZ', -100, 100, 0.01).onChange(this.setPosition);

                // initially hide gui
                dat.GUI.toggleHide();

                this.container = document.getElementById(this.containerId);
                document.getElementById('file-controls').appendChild(this.gui.domElement);
                this.containerWidth = this.container.clientWidth;
                this.containerHeight = this.container.clientHeight;

                this.renderer = new WebGLRenderer({
                    antialias: true
                });
                this.renderer.setPixelRatio(window.devicePixelRatio);
        		this.renderer.setSize(this.containerWidth, this.containerHeight);
        		this.renderer.shadowMap.enabled = true;
        		this.renderer.shadowMap.type = PCFSoftShadowMap;
        		this.renderer.gammaInput = true;
        		this.renderer.gammaOutput = true;
                this.camera = new PerspectiveCamera(45, this.containerWidth/this.containerHeight, 0.1, 2000);
                this.camera.position.set(5, 0, 0);
                this.camera.lookAt(new Vector3(0, 0, 0));
                this.camera.up.set(0, 1, 0);

                this.scene = new Scene();
                this.group = new Group();

                this.initEventListeners();
                this.initLights();
                this.initControls();
                if(navigator.getVRDisplays) {
                    this.container.appendChild(VRButton.createButton(this.renderer));
                    this.initViveControls();
                }

                this.container.appendChild(this.renderer.domElement);
                this.scene.add(this.camera);
                this.scene.add(this.group);
        		this.scene.add(new GridHelper(100, 10));
            },
            selectObject(object) {
                dat.GUI.toggleHide();
                if(object) {
                    this.addTransformControlsTo(object);
                } else {
                    this.removeTransformControls();
                }
            },
            addTransformControlsTo(mesh) {
                this.activeMesh = mesh;
                this.scene.add(this.transformControls);
                this.transformControls.attach(mesh);
                this.transformControls.enabled = true;
                this.controls.target = mesh.position.clone();
                this.controls.update();
            },
            removeTransformControls() {
                this.transformControls.detach();
                this.activeMesh = null;
                this.scene.remove(this.transformControls);
                this.transformControls.enabled = false;
                this.controls.target = new Vector3(0, 0, 0);
                this.controls.update();
            },
            zoomToObject(object) {
                const offset = 1.25;
                if(!object.geometry.boundingBox) {
                    object.geometry.computeBoundingBox();
                }
                const bbox = object.geometry.boundingBox;
                const center = bbox.getCenter();
                let size = bbox.getSize();
                size.multiply(object.scale);

                const maxDim = Math.max(size.x, size.y, size.z);
                const fov = this.camera.fov * (Math.PI/180);
                const z = Math.abs(maxDim / Math.sin(fov/2));

                this.scene.updateMatrixWorld();
                const objWP = object.getWorldPosition();

                const dir = this.camera.position.sub(objWP);
                const unitDir = dir.normalize();
                unitDir.multiplyScalar(z);
                let newPos = new Vector3();
                newPos.add(objWP);
                newPos.add(unitDir);
                this.camera.position.copy(newPos);
                this.camera.lookAt(object.position);
	            this.camera.updateProjectionMatrix();
                this.controls.update();
            },
            loadAllSubModels: function() {
                const vm = this;
                if(!vm.entity && !vm.entity.id) return;
                const id = vm.entity.id;
                $httpQueue.add(() => vm.$http.get(`/file/${id}/sub_files?c=3d`).then(function(response) {
                    const models = response.data;
                    models.forEach(m => vm.loadModel(m));
                }));
            },
            loadModel: function(file) {
                const fileType = this.getFileType(file);
                if(!fileType) return;
                switch(fileType) {
                    case 'dae':
                        this.loadCollada(file);
                        break;
                    case 'gltf':
                        this.loadGltf(file);
                        break;
                    case 'obj':
                        this.loadObj(file);
                        break;
                    case 'fbx':
                        this.loadFbx(file);
                        break;
                    case 'pdb':
                        this.loadProteinDb(file);
                        break;
                    case 'stl':
                        this.loadStl(file);
                        break;
                    case 'gcode':
                        this.loadGcode(file);
                        break;
                }
            },
            toggleFullscreen() {
                if(!this.fullscreenHandler) return;
                this.fullscreenHandler.toggle(document.getElementById(this.containerId))
            },
            animate: function() {
                this.animationId = requestAnimationFrame(this.animate);
                if(this.animationMixer.update) {
                     this.animationMixer.update(this.animationClock.getDelta());
                }
                this.render();
            },
            render: function() {
                if(!this.renderer) return;
                if(this.renderer.vr && this.renderer.vr.enabled) {
                    this.grabController.update();
                    this.flashlightController.update();
                }
                this.renderer.render(this.scene, this.camera);
                if(this.labelRenderer) {
                    this.labelRenderer.render(this.scene, this.camera);
                }
            },
            initEventListeners: function() {
                window.addEventListener('resize', this.onWindowResize, false);
                this.renderer.domElement.addEventListener('mousedown', this.onMouseDown, false);
                this.renderer.domElement.addEventListener('mousemove', this.onMouseMove, false);
                this.renderer.domElement.addEventListener('mouseup', this.onMouseUp, false);
            },
            initLights: function() {
                this.hemisphereLight = new HemisphereLight(0x808080, 0x606060);
                this.directionalLight = new DirectionalLight(0xffffff);
                this.directionalLight.position.set(this.camera.position);
                this.directionalLight.castShadow = true;
        		this.directionalLight.shadow.camera.top = 2;
        		this.directionalLight.shadow.camera.bottom = -2;
        		this.directionalLight.shadow.camera.right = 2;
        		this.directionalLight.shadow.camera.left = -2;
        		this.directionalLight.shadow.mapSize.set(4096, 4096);
                this.scene.add(this.hemisphereLight);
        		this.scene.add(this.directionalLight);
            },
            initControls: function() {
                this.controls = new OrbitControls(this.camera, this.renderer.domElement);
                this.transformControls = new TransformControls(this.camera, this.renderer.domElement);
                this.transformControls.enabled = false;

                this.transformControls.addEventListener('change', _ => {
                    if(this.transformControls.object) {
                        const s = this.transformControls.object.scale;
                        const p = this.transformControls.object.position;
                        this.guiConfig.scaleX = s.x;
                        this.guiConfig.scaleY = s.y;
                        this.guiConfig.scaleZ = s.z;
                        this.guiConfig.positionX = p.x;
                        this.guiConfig.positionY = p.y;
                        this.guiConfig.positionZ = p.z;
                        for(let k in this.guiCtrl) {
                            this.guiCtrl[k].updateDisplay();
                        }
                    }
                    this.render();
                });
                // Disable orbit controls on gizmo drag
                this.transformControls.addEventListener('dragging-changed', event => {
                    this.controls.enabled = !event.value;
                });
                window.addEventListener('keydown', event => {
                    if(event.target.tagName.toUpperCase() == 'INPUT') return;
                    if(event.target.tagName.toUpperCase() == 'TEXTAREA') return;
                    switch(event.keyCode) {
                        case 17: // CTRL
                            this.transformControls.setTranslationSnap(1);
							this.transformControls.setRotationSnap(TMath.degToRad(15));
                            break;
                        case 32: // SPACE
                            if(this.transformControls.enabled) {
                                this.removeTransformControls();
                            }
                            break;
                        case 81: // Q
                            this.transformControls.setSpace(this.transformControls.space === 'local' ? 'world' : 'local');
                            break;
                        case 87: // W
                            this.transformControls.setMode("translate");
                            break;
                        case 69: // E
                            this.transformControls.setMode("rotate");
                            break;
                        case 82: // R
                            this.transformControls.setMode("scale");
                            break;
                        case 70: // F
                            if(this.transformControls.enabled) {
                                this.zoomToObject(this.activeMesh);
                            }
                            break;
                    }
                });
                window.addEventListener('keyup', event => {
                    if(event.target.tagName.toUpperCase() == 'INPUT') return;
                    if(event.target.tagName.toUpperCase() == 'TEXTAREA') return;
                    switch(event.keyCode) {
                        case 17: // CTRL
                            this.transformControls.setTranslationSnap(null);
							this.transformControls.setRotationSnap(null);
                            break;
                    }
                });
            },
            initViveEventListeners: function() {
                // Vive Events
                if(this.grabController) {
                    this.grabController.addEventListener('selectstart', this.onGrabDown);
                    this.grabController.addEventListener('selectend', this.onGrabUp);
                    this.grabController.addEventListener('thumbpadup', this.dimWorldLight);
                    // this.grabController.addEventListener('axischanged', this.recognizeTouch);
                }
                if(this.flashlightController) {
                    this.flashlightController.addEventListener('selectstart', this.onLightOn);
                    this.flashlightController.addEventListener('selectend', this.onLightOff);
                    this.flashlightController.addEventListener('thumbpadup', this.dimFlashLight);
                }

                window.addEventListener('vrdisplaypresentchange', this.vrDisplayStateChanged, false);
            },
            initViveControls: function() {
                if(!this.renderer.vr) {
                    return;
                }
                const vm = this;
                this.grabController = this.renderer.vr.getController(0);
                this.flashlightController = this.renderer.vr.getController(1);
                if(this.grabController || this.flashlightController) {
                    this.initViveEventListeners();
                    let ctrlLoader = new OBJLoader2();
                    ctrlLoader.load('./img/vr_controller_vive_1_5.obj', function(object) {
                        let txtLoader = new TextureLoader();
                        txtLoader.setPath('./img/vive-controller/');
                        let controllerModel = object.children[0];
                        controllerModel.material.map = txtLoader.load('./img/onepointfive_texture.png');
                        if(vm.grabController) {
                            vm.grabController.add(object.clone());
                        }
                        if(vm.flashlightController) {
                            vm.flashlightController.add(object.clone());
                        }
                    });
                    // Add Controller Rays
                    let geometry = new Geometry();
                    geometry.vertices.push(new Vector3(0, 0, 0));
                    geometry.vertices.push(new Vector3(0, 0, -1));
                    let line = new Line(geometry);
                    line.name = 'line';
                    line.scale.z = 5;

                    if(this.grabController) {
                        this.grabController.add(line.clone());
                        this.scene.add(this.grabController);
                    }

                    if(this.flashlightController) {
                        this.flashlight = new SpotLight(0xffffff, 1, 5, 0.15, 1, 2);
                        this.flashlight.castShadow = true;
                        this.flashlight.shadow.mapSize.width = 1024;
                        this.flashlight.shadow.mapSize.height = 1024;
                        this.flashlight.shadow.camera.near = 0.05;
                        this.flashlight.shadow.camera.far = 1000;
                        // switch light off, is triggered using button on the ctrl
                        this.flashlight.intensity = 0;
                        // position the light source right behind the ctrl
                        this.flashlight.position.set(
                            this.flashlightController.position.x,
                            this.flashlightController.position.y,
                            this.flashlightController.position.z + 0.05
                        );
                        this.flashlightController.add(this.flashlight);

                        // point the light source to the controller's "view" direction
                        let c2line = line.clone();
                        this.flashlightController.add(c2line);
                        this.flashlight.target = c2line;
                        this.flashlightController.add(this.flashlight.target);
                        this.scene.add(this.flashlightController);
                    }
                }
            },
            // Loaders
            loadCollada: function(file) {
                const loader = new ColladaLoader();
                loader.load(file.url, collada => {
                    const object = collada.scene;
                    if(object.rotation.x != 0) {
                        this.$showToast('Import Note', 'Your collada file has an up axis different from Y_UP.', 'warn', 5000)
                        object.rotation.x = 0;
                    }
                    this.addModelToScene(object);
                },
                event => { // onProgress
                    this.updateProgress(event);
                },
                event => { // onError
                });
            },
            loadGltf: function(file) {
                const loader = new GLTFLoader();
                loader.load(file.url, data => {
                    const gltf = data;
                    const object = gltf.scene;
                    this.addModelToScene(object, gltf.animations);
                }, event => {
                    this.updateProgress(event);
                }, error => {
                });
            },
            loadObj: function(file) {
                const url = file.url;
                const sep = url.lastIndexOf('/')+1;
                const path = url.substr(0, sep);
                const filename = url.substr(sep);
                // we assume that the mtl file has the same name as the obj file
                const mtlname = filename.substr(0, filename.lastIndexOf('.')) + '.mtl';
                const mtlLoader = new MTLLoader();
                mtlLoader.setMaterialOptions({
                    side: DoubleSide
                });
                mtlLoader.setPath(path);
                // try to load mtl file
                mtlLoader.load(mtlname, materials => {
                    // load obj file with loaded materials
                    this.loadObjModel(path, filename, materials);
                }, event => {
                    this.updateProgress(event);
                }, event => {
                    // onError: try to load obj without materials
                    this.loadObjModel(path, filename, undefined);
                });
            },
            loadObjModel: function(path, filename, materials) {
                let objLoader = new OBJLoader2();
                objLoader.setModelName(filename);
                if(materials) {
                    objLoader.addMaterials(MtlObjBridge.addMaterialsFromMtlLoader(materials));
                }
                objLoader.load(path + filename,
                    object => { // onSuccess
                        this.addModelToScene(object);
                    },
                    event => { // onProgress
                        this.updateProgress(event);
                    },
                    event => { // on
                    }
                );
            },
            loadFbx: function(file) {
                const url = file.url;
                const loader = new FBXLoader();
                loader.load(url, object => {
                    this.addModelToScene(object);
                }, event => {
                    this.updateProgress(event);
                }, event => {
                });
            },
            loadStl: function(file) {
                const url = file.url;
                const loader = new STLLoader();
                loader.load(url, geometry => {
                    const material = new MeshPhongMaterial({
                        color: 0xAA5555,
                        specular: 0x111111,
                        shininess: 200,
                        vertexColors: geometry.hasColors,
                        opacity: geometry.hasColors ? geometry.alpha : 1,
                    });
                    const object = new Mesh(geometry, material);
                    this.addModelToScene(object);
                }, event => {
                    this.updateProgress(event);
                }, event => {
                });
            },
            loadGcode: function(file) {
                const url = file.url;
                const loader = new GCodeLoader();
                loader.load(url, object => {
                    this.addModelToScene(object);
                }, event => {
                    this.updateProgress(event);
                }, event => {
                });
            },
            loadProteinDb: function(file) {
                const vm = this;
                let labelRenderer = new CSS2DRenderer();
                labelRenderer.setSize(this.containerWidth, this.containerHeight);
				labelRenderer.domElement.style.position = 'absolute';
				labelRenderer.domElement.style.top = '0';
				labelRenderer.domElement.style.pointerEvents = 'none';
                this.container.appendChild(labelRenderer.domElement);
                this.loadMolecules(file.url);
            },
            loadMolecules: function(url) {
                const vm = this;
                while(this.group.children.length > 0) {
                    let object = this.group.children[0];
                    object.parent.remove(object);
                }
                let pdbLoader = new PDBLoader();
                pdbLoader.load(url, function(pdb) {
                    var atoms = pdb.geometryAtoms;
                    let bonds = pdb.geometryBonds;
                    let json = pdb.json;

                    let boxGeometry = new BoxBufferGeometry(1, 1, 1);
                    let sphereGeometry = new IcosahedronBufferGeometry(1, 2);

                    let offset = atoms.center();
                    bonds.translate(offset.x, offset.y, offset.z);

                    let positions = atoms.getAttribute('position');
                    let colors = atoms.getAttribute('color');

                    let position = new Vector3();
                    let color = new Color();

                    for(let i=0; i<positions.count; i++) {
                        position.x = positions.getX(i);
                        position.y = positions.getY(i);
                        position.z = positions.getZ(i);

                        color.r = colors.getX(i);
                        color.g = colors.getY(i);
                        color.b = colors.getZ(i);

                        let material = new MeshPhongMaterial({color: color});

                        let object = new Mesh(sphereGeometry, material);
                        object.position.copy(position);
                        object.position.multiplyScalar(1);
                        object.scale.multiplyScalar(0.33);
                        vm.group.add(object);

                        let atom = json.atoms[i];
        				let text = document.createElement('div');
        				text.className = 'atom-label';
        				text.style.color = 'rgb(' + atom[3][0] + ',' + atom[3][1] + ',' + atom[3][2] + ')';
        				text.textContent = atom[4];
        				let label = new CSS2DObject(text);
        				label.position.copy(object.position);
        				vm.group.add(label);
                    }

                    positions = bonds.getAttribute('position');
                    let start = new Vector3();
                    let end = new Vector3();

                    for(let i=0; i<positions.count; i+=2) {
                        start.x = positions.getX(i);
                        start.y = positions.getY(i);
                        start.z = positions.getZ(i);

                        end.x = positions.getX(i+1);
                        end.y = positions.getY(i+1);
                        end.z = positions.getZ(i+1);

                        start.multiplyScalar(1);
                        end.multiplyScalar(1);

                        let object = new Mesh(boxGeometry, new MeshPhongMaterial(0xffffff));
                        object.position.copy(start);
                        object.position.lerp(end, 0.5);
                        object.scale.set(0.1, 0.1, start.distanceTo(end));
                        object.lookAt(end);
                        vm.group.add(object);
                    }
                    vm.onWindowResize();
                },
                function(event) { // onProgress
                    vm.updateProgress(event);
                });
            },
            addModelToScene(model, extAnimations) {
                // Play first animation if available
                const animations = extAnimations ? extAnimations : model.animations;
                if(animations && animations.length) {
                    this.animationMixer = new AnimationMixer(model);
                    this.animationMixer.clipAction(animations[0]).play();
                }

                const mid = model.uuid;
                const isLod = this.hasModelLods(model);
                let lod = new LOD();

                for(let i=0; i<model.children.length; i++) {
                    let node = model.children[i].clone();
                    if(isLod) {
                        this.lodParts[node.uuid] = mid;
                    }
                    if(node.isMesh) {
                        node.castShadow = true;
                        node.receiveShadow = true;
                        if(node.material) {
                            node.material.side = DoubleSide;
                        }
                        node.geometry.computeBoundingBox();
                        const offset = node.geometry.boundingBox.getCenter();
                        node.geometry.applyMatrix4(new Matrix4().makeTranslation(-offset.x, -offset.y, -offset.z));
                        node.position.copy(offset);
                        if(isLod) {
                            lod.addLevel(node, (i+1) * 10);
                            this.raycastTargets.push(node);
                        } else {
                            // push original node if not a LoD, because
                            // original model is added to group later
                            this.raycastTargets.push(model.children[i]);
                        }
                    }
                }
                if(isLod) {
                    this.lodGroup[mid] = lod;
                    this.group.add(lod);
                } else {
                    this.group.add(model);
                }
                this.onWindowResize();
            },
            intersectAtClick(event) {
                this.mouse.x = (event.layerX / this.renderer.domElement.clientWidth) * 2 - 1;
                this.mouse.y = -(event.layerY / this.renderer.domElement.clientHeight) * 2 + 1;
                this.raycaster.setFromCamera(this.mouse, this.camera);

                return this.raycaster.intersectObjects(this.raycastTargets);
            },
            getIntersections: function(controller) {
        		this.tempMatrix.identity().extractRotation(controller.matrixWorld);
        		this.raycaster.ray.origin.setFromMatrixPosition(controller.matrixWorld);
        		this.raycaster.ray.direction.set(0, 0, -1).applyMatrix4(this.tempMatrix);
        		return this.raycaster.intersectObjects(this.group.children, true);
        	},
            hasModelLods(objectGroup) {
                if(objectGroup.type != 'Group') return false;
                if(!objectGroup.children) return false;
                const regex = RegExp('(LOD|lod)\\d+$');
                let isLod = true;
                for(let i=0; i<objectGroup.children.length; i++) {
                    const c = objectGroup.children[i];
                    if(!regex.test(c.name)) {
                        isLod = false;
                        break;
                    }
                }
                return isLod;
            },
            isLodGroup(model) {
                return !!this.lodGroup[model.uuid];
            },
            isPartOfLodGroup(mesh) {
                const part = this.lodParts[mesh.uuid];
                if(!part)  return false;
                return !!this.lodGroup[part];
            },
            getLodGroup(mesh) {
                // TODO throw error instead?
                if(!this.isPartOfLodGroup(mesh)) return {};
                return this.lodGroup[this.lodParts[mesh.uuid]];
            },
            //EventListeners
            // track if primary button is pressed
            onMouseDown(event) {
                // only capture left (primary) button click
                if(event.buttons !== 1) return;
                // do not capture event, if transform controls are active
                if(this.transformControls.enabled) return;
                this.mouseDown = event.buttons;
            },
            // track if primary button is dragged
            onMouseMove(event) {
                // only capture left (primary) button click
                if(event.buttons !== 1) return;
                // do not capture event, if transform controls are active
                if(this.transformControls.enabled) return;
                if(this.mouseDown !== 1) return;
                this.mouseMoving = true;
            },
            // handle click only if primary button and not dragged
            onMouseUp(event) {
                if(this.mouseMoving || this.mouseDown !== 1) {
                    this.mouseDown = 0;
                    this.mouseMoving = false;
                    return;
                }
                // do not capture event, if transform controls are active
                if(this.transformControls.enabled) return;
                const intersections = this.intersectAtClick(event);
                if(intersections.length) {
                    // if current model is part of a LOD group, select group
                    let mesh = intersections[0].object;
                    if(!this.isLodGroup(mesh) && this.isPartOfLodGroup(mesh)) {
                        mesh = this.getLodGroup(mesh);
                    }
                    this.selectObject(mesh);
                }
            },
            onWindowResize: function() {
                this.containerWidth = this.renderer.domElement.parentElement.clientWidth;
                this.containerHeight = this.renderer.domElement.parentElement.clientHeight;
                this.camera.aspect = this.containerWidth/this.containerHeight;
                this.camera.updateProjectionMatrix();
                this.renderer.setSize(this.containerWidth, this.containerHeight);
                if(this.labelRenderer) {
                    this.labelRenderer.setSize(this.containerWidth, this.containerHeight);
                }
            },
            // Vive EventListeners
            onGrabDown: function(event) {
        		let controller = event.target;
        		const intersections = this.getIntersections(controller);
        		if(intersections.length) {
        			const intersection = intersections[0];
        			this.tempMatrix.getInverse(controller.matrixWorld);
        			let object = intersection.object;
        			object.matrix.premultiply(this.tempMatrix);
        			object.matrix.decompose(object.position, object.quaternion, object.scale);
        			controller.add(object);
        			controller.userData.selected = object;
        		}
            },
            onGrabUp: function(event) {
        		let controller = event.target;
        		if(controller.userData.selected !== undefined) {
        			let object = controller.userData.selected;
        			object.matrix.premultiply(controller.matrixWorld);
        			object.matrix.decompose(object.position, object.quaternion, object.scale);
        			this.group.add(object);
        			controller.userData.selected = undefined;
        		}
            },
            onLightOn: function(event) {
        		this.flashlight.intensity = this.flashlightIntensity;
        		this.flashlightOn = true;
            },
            onLightOff: function(event) {
                this.flashlight.intensity = 0;
        		this.flashlightOn = false;
            },
            dimFlashLight: function(event) {
        		// thumbpad values are from -1 to 1, intesity goes from 0 to 2
        		this.flashlightIntensity = event.axes[0] + 1;
        		if(this.flashlightOn) this.flashlight.intensity = this.flashlightIntensity;
            },
            vrDisplayStateChanged(event) {
                if(this.renderer) {
                    this.renderer.vr.enabled = event.display.isPresenting;
                }
            },
            dimWorldLight: function(event) {
        		// thumbpad values are from -1 to 1, intesity goes from 0 to 2
        		const hem = event.axes[0];
        		const dir = event.axes[1];
        		// only update the intensity for the light with the higher value
        		// (emulate a d-pad)
        		if(Math.abs(hem) > Math.abs(dir)) {
        			this.hemisphereIntensity = hem + 1;
        			this.hemisphereLight.intensity = this.hemisphereIntensity;
        		} else {
        			this.directionalIntensity = dir + 1;
        			this.directionalLight.intensity = this.directionalIntensity;
        		}
            },
            updateProgress: function(event) {
                if(event.lengthComputable) {
                    this.progress = Math.round(event.loaded / event.total * 100);
                }
            }
        },
        data() {
            return {
                container: {},
                containerId: 'three-container',
                progress: 0,
                // three
                scale: 1,
                gui: null,
                guiCtrl: {},
                guiConfig: null,
                animationClock: new Clock(),
                animationId: -1,
                animationMixer: {},
                camera: {},
                controls: {},
                transformControls: {},
                activeMesh: null,
                containerWidth: 200,
                containerHeight: 100,
                directionalLight: {},
                heimisphereLight: {},
                group: {},
                lodGroup: {},
                lodParts: {},
                raycastTargets: [],
                mouse: new Vector2(),
                mouseDown: 0,
                mouseMoving: false,
                raycaster: new Raycaster(),
                renderer: {},
                scene: {},
	            tempMatrix: new Matrix4(),
                // controllers
                grabController: null,
                flashlightController: null,
                flashlight: {},
                flashlightOn: false,
                flashlightIntensity: 1,
                hemisphereIntensity: 1,
                directionalIntensity: 1
            }
        },
        watch: {
            file(newFile, oldFile) {
                if(newFile && oldFile && newFile.id == oldFile.id) return;
                this.loadModel(newFile);
            }
        }
    }
</script>
