<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-people"></i> Usuarios</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <router-link class="nav-link" to="/perfil"><i class="icon-user"></i> <em>Perfil</em></router-link>
                    </li>
                </ul>
            </li>
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-folder-alt"></i> DOCUMENTOS</a>
                <ul class="nav-dropdown-items">
                    <li class="nav-item">
                        <router-link class="nav-link" to="/documento/ciudadano"><i class="icon-doc"></i> <em>Nuevo Documento</em></router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/enviados"><i class="icon-doc"></i> <em>Enviados</em></router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/seguimiento"><i class="icon-doc"></i> <em>Seguimiento</em></router-link>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
