<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li @click="menu=0" class="nav-item">
                <a class="nav-link" href="#"><i class="icon-speedometer"></i> ESCRITORIO</a>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-layers"></i> ENTIDADES</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=1" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-layers"></i> <em>Entidad</em></a>
                    </li>
                    <li @click="menu=2" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-trash"></i> <em>Papelera</em></a>
                    </li>
                </ul>
            <li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-list"></i> DEPENDENCIAS</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=3" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-list"></i> <em>Dependencia</em></a>
                    </li>
                    <li @click="menu=4" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-trash"></i> <em>Papelera</em></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-people"></i> USUARIOS</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=5" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-user"></i> <em>Usuario</em></a>
                    </li>
                    <li @click="menu=29" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-user"></i> <em>Perfil</em></a>
                    </li>
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-trash"></i> <em>Papelera</em></a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=6" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-trash"></i> <em>Usuarios</em></a>
                            </li>
                            <li @click="menu=7" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-trash"></i> <em>Cargos</em></a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-settings"></i> TUPA</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=8" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-settings"></i> <em>Módulos</em></a>
                    </li>
                </ul>
                <ul class="nav-dropdown-items">
                    <li @click="menu=9" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-settings"></i> <em>Tipo Atención</em></a>
                    </li>
                </ul>
                <ul class="nav-dropdown-items">
                    <li @click="menu=10" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-settings"></i> <em>Crear Procedimiento</em></a>
                    </li>
                </ul>
                <ul class="nav-dropdown-items">
                    <li @click="menu=32" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-settings"></i> <em>Procedimientos</em></a>
                    </li>
                </ul>
                <ul class="nav-dropdown-items">
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-trash"></i> <em>Papelera</em></a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=22" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-trash"></i> <em>Módulos</em></a>
                            </li>
                            <li @click="menu=23" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-trash"></i> <em>Tipo Atención</em></a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-folder-alt"></i> DOCUMENTOS</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-settings"></i> <em>Configuración</em></a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=12" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-settings"></i> <em>Tipo Documento</em></a>
                            </li>
                            <li @click="menu=15" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-settings"></i> <em>Origen Documento</em></a>
                            </li>
                            <li @click="menu=14" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-settings"></i> <em>Proveído Documento</em></a>
                            </li>
                            <li @click="menu=16" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-settings"></i> <em>Estado Trámite</em></a>
                            </li>
                            <li @click="menu=17" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-settings"></i> <em>Estado Procedimiento</em></a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-trash"></i> <em>Papelera</em></a>
                        <ul class="nav-dropdown-items">
                            <li @click="menu=13" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-trash"></i> <em>Tipo documento</em></a>
                            </li>
                            <li @click="menu=18" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-trash"></i> <em>Origen documento</em></a>
                            </li>
                            <li @click="menu=19" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-trash"></i> <em>Proveído documento</em></a>
                            </li>
                            <li @click="menu=20" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-trash"></i> <em>Estado trámite</em></a>
                            </li>
                            <li @click="menu=21" class="nav-item">
                                <a class="nav-link" href="#"><i class="icon-trash"></i> <em>Estado procedimiento</em></a>
                            </li>
                        </ul>
                    </li>
                    <li @click="menu=11" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-folder"></i> <em>Nuevo Documento</em></a>
                    </li>
                    <li @click="menu=24" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-folder"></i> <em>Pendientes</em></a>
                    </li>
                    <li @click="menu=25" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-folder"></i> <em>Tramitar</em></a>
                    </li>
                    <li @click="menu=26" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-folder"></i> <em>Enviados</em></a>
                    </li>
                    <li @click="menu=27" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-folder"></i> <em>Archivo</em></a>
                    </li>
                    <li @click="menu=28" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-folder"></i> <em>Seguimiento</em></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-chart"></i> REPORTES</a>
                <ul class="nav-dropdown-items">
                    <li @click="menu=0" class="nav-item">
                        <a class="nav-link" href="#"><i class="icon-chart"></i> <em>Reporte</em></a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
