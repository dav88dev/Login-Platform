<template>
    <div class="container-fluid">
        <video class="background" v-if="backgroundShowOrHideVideo" autoplay loop muted>
            <source :src="media.src">
        </video>
        <img class="background" v-if="backgroundShowOrHidePicture" alt="image" :src="media.src"/>
        <transition name="entry" appear mode="out-in">
            <component :is="activeComponent"></component>
        </transition>
    </div>
</template>
<style lang="scss">
    @import '~bootstrap/scss/bootstrap';
</style>
<script>
    import {mapGetters} from 'vuex';
    import {windowSize} from '../../mixins/windowSize';

    const Login = resolve => {
        require.ensure(['./Login.vue'], () => {
            resolve(require('./Login.vue'));
        });
    };

    const Policy = resolve => {
        require.ensure(['./Policy.vue'], () => {
            resolve(require('./Policy.vue'));
        });
    };

    const Email = resolve => {
        require.ensure(['./Email.vue'], () => {
            resolve(require('./Email.vue'));
        });
    };

    const Social = resolve => {
        require.ensure(['./Social.vue'], () => {
            resolve(require('./Social.vue'));
        });
    };


    export default {
        name: 'home',
        data() {
            return {}
        },

        components: {
            Login,
            Policy,
            Email,
            Social
        },

        mixins: [windowSize],


        computed: {
            // whenever the document is resized, re-set the 'fullHeight' variable

            backgroundShowOrHideVideo() {
                if (this.windowWidth > 576 && this.media.type == 'mp4' || this.windowWidth > 576 && this.media.type == 'mpeg4') {
                    return true
                } else {
                    return false
                }
            },

            backgroundShowOrHidePicture() {
                if (this.windowWidth > 576 && this.media.type == 'jpg' || this.windowWidth > 576 && this.media.type == 'jpeg') {
                    return true
                } else {
                    return false
                }
            },
            ...mapGetters([
                'activeComponent',
                'media',
            ]),
        },

    }
</script>

<style scoped>

    .background {
        position: fixed;
        top: 50%;
        left: 50%;
        min-width: 100%;
        min-height: 100%;
        width: auto;
        height: auto;
        z-index: -100;
        -webkit-transform: translateX(-50%) translateY(-50%);
        transform: translateX(-50%) translateY(-50%);
        background-size: contain;
    }

    .vertical-center {
        min-height: 100%; /* Fallback for vh unit */
        min-height: 100vh;
        /* You might also want to use
                               'height' property instead.

                               Note that for percentage values of
                               'height' or 'min-height' properties,
                               the 'height' of the parent element
                               should be specified explicitly.

                               In this case the parent of '.vertical-center'
                               is the <body> element */

        /* Make it a flex container */
        display: -webkit-box;
        display: -moz-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;

        /* Align the bootstrap's container vertically */
        -webkit-box-align: center;
        -webkit-align-items: center;
        -moz-box-align: center;
        -ms-flex-align: center;
        align-items: center;

        /* In legacy web browsers such as Firefox 9
           we need to specify the width of the flex container */
        width: 100%;

        /* Also 'margin: 0 auto' doesn't have any effect on flex items in such web browsers
           hence the bootstrap's container won't be aligned to the center anymore.

           Therefore, we should use the following declarations to get it centered again */
        -webkit-box-pack: center;
        -moz-box-pack: center;
        -ms-flex-pack: center;
        -webkit-justify-content: center;
        justify-content: center;
    }

    .entry-enter {

        opacity: 0;
    }

    .entry-enter-active {
        transition: opacity 0.6s;

    }

    .entry-leave {
        opacity: 1;

    }

    .entry-leave-active {
        transition: opacity 0.6s;
        opacity: 0;

    }

    @media screen and (max-width: 576px) {
        .container-fluid {
            padding: 0;
        }

    }

</style>