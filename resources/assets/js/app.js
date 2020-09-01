require('./bootstrap');

import Vue from 'vue'

import { ElementTiptapPlugin } from 'element-tiptap';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import 'element-tiptap/lib/index.css';

Vue.use(ElementUI);
Vue.use(ElementTiptapPlugin);

import router from './routes'

Vue.component('notification', require('./components/Notification').default);
Vue.component('user', require('./views/users/User').default);
Vue.component('user-trashed', require('./views/users/UserTrashed').default);
Vue.component('charge-trashed', require('./views/users/ChargeTrashed').default);
Vue.component('dependency', require('./views/dependencies/Dependency').default);
Vue.component('trashed-dependency', require('./views/dependencies/TrashedDependency').default);
Vue.component('document-administrator', require('./views/documents/DocumentAdministrator').default);
Vue.component('document-internal', require('./views/documents/DocumentInternal').default);
Vue.component('document-external', require('./views/documents/DocumentExternal').default);
Vue.component('document-type', require('./views/documents/DocumentType').default);
Vue.component('document-type-trashed', require('./views/documents/DocumentTypeTrashed').default);
Vue.component('action-document', require('./views/documents/ActionDocument').default);
Vue.component('action-document-trashed', require('./views/documents/ActionDocumentTrashed').default);
Vue.component('document-origin', require('./views/documents/DocumentOrigin').default);
Vue.component('document-origin-trashed', require('./views/documents/DocumentOriginTrashed').default);
Vue.component('procedure-state', require('./views/documents/ProcedureState').default);
Vue.component('procedure-state-trashed', require('./views/documents/ProcedureStateTrashed').default);
Vue.component('process-state', require('./views/documents/ProcessState').default);
Vue.component('process-state-trashed', require('./views/documents/ProcessStateTrashed').default);
Vue.component('entity', require('./views/entities/Entity').default);
Vue.component('trashed-entity', require('./views/entities/TrashedEntity').default);
Vue.component('module', require('./views/tupa/Module').default);
Vue.component('module-trashed', require('./views/tupa/ModuleTrashed').default);
Vue.component('attention-type', require('./views/tupa/AttentionType').default);
Vue.component('attention-type-trashed', require('./views/tupa/AttentionTypeTrashed').default);
Vue.component('make-procedure', require('./views/tupa/MakeProcedure').default);
Vue.component('procedure', require('./views/tupa/Procedure').default);
Vue.component('document-pending', require('./views/documents/DocumentPending').default);
Vue.component('document-process', require('./views/documents/DocumentProcess').default);
Vue.component('document-sent', require('./views/documents/DocumentSent').default);
Vue.component('document-archive', require('./views/documents/DocumentArchive').default);
Vue.component('document-tracing', require('./views/documents/DocumentTracing').default);
Vue.component('manage-external', require('./views/documents/ManageExternal').default);
Vue.component('document-report', require('./views/reports/DocumentReport').default);
Vue.component('view-login', require('./views/login/ViewLogin').default);
Vue.component('user-profile', require('./views/users/UserProfile').default);
Vue.component('chart-document', require('./views/charts/ChartDocument').default);
Vue.component('view-process', require('./views/login/ViewProcess').default);
Vue.component('view-tracing', require('./views/login/ViewTracing').default);


const app = new Vue({
    el: '#app',
    router,
    data: {
        notifications: [],
        ruta: 'http://165.22.34.176',
    }
});