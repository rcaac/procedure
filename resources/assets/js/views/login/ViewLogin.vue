<template>
    <login :ruta="ruta"/>
</template>

<script>
import Login from '../../components/login/Login';
export default {
    components: {Login},
    props: ['ruta'],
}
</script>
