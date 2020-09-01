<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-people"></i> USUARIOS</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <router-link class="nav-link" to="/perfil"><i class="icon-user"></i> <em>Perfil</em></router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-folder"></i> DOCUMENTOS</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <router-link class="nav-link" to="/documento/usuario"><i class="icon-doc"></i> <em>Nuevo Documento</em></router-link>
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
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
