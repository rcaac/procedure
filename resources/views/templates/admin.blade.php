<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-folder"></i> DOCUMENTOS</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <router-link class="nav-link" to="/documento"><i class="icon-doc"></i> <em>Nuevo Documento</em></router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/pendientes"><i class="icon-doc"></i> <em>Pendientes</em></router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/tramitar"><i class="icon-doc"></i> <em>Tramitar</em></router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/enviados"><i class="icon-doc"></i> <em>Enviados</em></router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/archivo"><i class="icon-doc"></i> <em>Archivo</em></router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/seguimiento"><i class="icon-doc"></i> <em>Seguimiento</em></router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-book-open"></i> GESTIÓN</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <router-link class="nav-link" to="/documento/interno"><i class="icon-book-open"></i> <em>Documentos Internos</em></router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/documento/externo"><i class="icon-book-open"></i> <em>Documentos Externos</em></router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-chart"></i> REPORTES</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <router-link class="nav-link" to="/reportes"><i class="icon-doc"></i> <em>Reportes</em></router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/graficas"><i class="icon-chart"></i> <em>Gráficas</em></router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-settings"></i> CONFIGURACIÓN</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-layers"></i> Entidades</a>
                        <ul class="nav-dropdown-items">
                            <li class="nav-item">
                                <router-link class="nav-link" to="/entidades"><i class="icon-layers"></i> <em>Entidad</em></router-link>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-list"></i> Dependencias</a>
                        <ul class="nav-dropdown-items">
                            <li class="nav-item">
                                <router-link class="nav-link" to="/dependencias"><i class="icon-list"></i> <em>Dependencia</em></router-link>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-people"></i> Usuarios</a>
                        <ul class="nav-dropdown-items">
                            <li class="nav-item">
                                <router-link class="nav-link" to="/usuarios"><i class="icon-user"></i> <em>Usuario</em></router-link>
                            </li>
                            <li class="nav-item">
                                <router-link class="nav-link" to="/perfil"><i class="icon-user"></i> <em>Perfil</em></router-link>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-puzzle"></i> Tupa</a>
                        <ul class="nav-dropdown-items">
                            <li class="nav-item">
                                <router-link class="nav-link" to="/procedimiento/crear"><i class="icon-puzzle"></i> <em>Crear Procedimiento</em></router-link>
                            </li>
                            <li class="nav-item">
                                <router-link class="nav-link" to="/procedimiento"><i class="icon-puzzle"></i> <em>Procedimientos</em></router-link>
                            </li>
                            <li class="nav-item">
                                <router-link class="nav-link" to="/modulo"><i class="icon-puzzle"></i> <em>Módulos</em></router-link>
                            </li>
                            <li class="nav-item">
                                <router-link class="nav-link" to="/atencion/tipo"><i class="icon-puzzle"></i> <em>Tipo Atención</em></router-link>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-folder"></i> <em>Documentos</em></a>
                        <ul class="nav-dropdown-items">
                            <li class="nav-item">
                                <router-link class="nav-link" to="/documento/tipo"><i class="icon-doc"></i> <em>Tipo Documento</em></router-link>
                            </li>
                            <li class="nav-item">
                                <router-link class="nav-link" to="/documento/origen"><i class="icon-doc"></i> <em>Origen Documento</em></router-link>
                            </li>
                            <li class="nav-item">
                                <router-link class="nav-link" to="/documento/proveido"><i class="icon-doc"></i> <em>Proveído Documento</em></router-link>
                            </li>
                            <li class="nav-item">
                                <router-link class="nav-link" to="/documento/estado/tramite"><i class="icon-doc"></i> <em>Estado Trámite</em></router-link>
                            </li>
                            <li class="nav-item">
                                <router-link class="nav-link" to="/documento/estado/procedimiento"><i class="icon-doc"></i> <em>Estado Procedimiento</em></router-link>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
