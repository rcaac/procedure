import Vue from 'vue';

import Router from 'vue-router';

import User from './views/users/User';
import Dependency from './views/dependencies/Dependency';
import DocumentAdministrator from './views/documents/DocumentAdministrator';
import DocumentInternal from './views/documents/DocumentInternal';
import DocumentExternal from './views/documents/DocumentExternal';
import DocumentType from './views/documents/DocumentType';
import ActionDocument from './views/documents/ActionDocument';
import DocumentOrigin from './views/documents/DocumentOrigin';
import ProcedureState from './views/documents/ProcedureState';
import ProcessState from './views/documents/ProcessState';
import Entity from './views/entities/Entity';
import Module from './views/tupa/Module';
import AttentionType from './views/tupa/AttentionType';
import MakeProcedure from './views/tupa/MakeProcedure';
import Procedure from './views/tupa/Procedure';
import DocumentPending from './views/documents/DocumentPending';
import DocumentProcess from './views/documents/DocumentProcess';
import DocumentSent from './views/documents/DocumentSent';
import DocumentSentExternal from './views/documents/DocumentSentExternal';
import DocumentArchive from './views/documents/DocumentArchive';
import DocumentTracing from './views/documents/DocumentTracing';
import ManageInternal from './views/documents/ManageInternal';
import ManageExternal from './views/documents/ManageExternal';
import DocumentReport from './views/reports/DocumentReport';
import UserProfile from './views/users/UserProfile';
import ChartDocument from './views/charts/ChartDocument';

Vue.use(Router);

const router = new Router({
        routes: [
            {
                path: '/entidades',
                component: Entity
            },
            {
                path: '/dependencias',
                component: Dependency
            },
            {
                path: '/usuarios',
                component: User
            },
            {
                path: '/perfil',
                component: UserProfile
            },
            {
                path: '/procedimiento/crear',
                component: MakeProcedure
            },
            {
                path: '/procedimiento',
                component: Procedure
            },
            {
                path: '/documento',
                component: DocumentAdministrator
            },
            {
                path: '/pendientes',
                component: DocumentPending
            },
            {
                path: '/documento/interno',
                component: ManageInternal
            },
            {
                path: '/documento/externo',
                component: ManageExternal
            },
            {
                path: '/tramitar',
                component: DocumentProcess
            },
            {
                path: '/enviados',
                component: DocumentSent
            },
            {
                path: '/externo/enviados',
                component: DocumentSentExternal
            },
            {
                path: '/archivo',
                component: DocumentArchive
            },
            {
                path: '/seguimiento',
                component: DocumentTracing
            },
            {
                path: '/reportes',
                component: DocumentReport
            },
            {
                path: '/graficas',
                component: ChartDocument
            },
            {
                path: '/modulo',
                component: Module
            },
            {
                path: '/atencion/tipo',
                component: AttentionType
            },
            {
                path: '/documento/tipo',
                component: DocumentType
            },
            {
                path: '/documento/origen',
                component: DocumentOrigin
            },
            {
                path: '/documento/proveido',
                component: ActionDocument
            },
            {
                path: '/documento/estado/tramite',
                component: ProcedureState
            },
            {
                path: '/documento/estado/procedimiento',
                component: ProcessState
            },
            {
                path: '/documento/usuario',
                component: DocumentInternal
            },
            {
                path: '/documento/ciudadano',
                component: DocumentExternal
            },
        ],
        linkExactActiveClass: 'active',
        mode: 'history'
    });

    router.beforeEach((to, from, next) => {
        next();
    });

    export default router;
