import {Toast, Loading, Modal, Button, ContextualSaveBar} from '@shopify/app-bridge/actions';
var loading = '';
export default {

    initLoading(){
        loading = Loading.create(window.shopify_app_bridge);
    },
    startLoading(){
        this.initLoading();
        loading.dispatch(Loading.Action.START);
    },
    stopLoading(){
        this.initLoading();
        loading.dispatch(Loading.Action.STOP);
    },
    successToast(message){
        let toastNotice = Toast.create(window.shopify_app_bridge, {message:message,duration: 2000});
        toastNotice.dispatch(Toast.Action.SHOW);
    },
    errorToast(message){
        let toastNotice = Toast.create(window.shopify_app_bridge, {message:message,duration: 2000,isError: true});
        toastNotice.dispatch(Toast.Action.SHOW);
    },
    confirmModel(){

        const okButton = Button.create(window.shopify_app_bridge, {label: 'Ok'});
        okButton.subscribe(Button.Action.CLICK, () => {
            console.log('Ok okButton');
        });
        const cancelButton = Button.create(window.shopify_app_bridge, {label: 'Cancel'});
        cancelButton.subscribe(Button.Action.CLICK, () => {
            console.log('Ok cancelButton');
        });
        const modalOptions = {
            title: 'My Modal',
            footer: {
                buttons: {
                    primary: okButton,
                    secondary: [cancelButton],
                },
            },
        };

        const myModal = Modal.create(window.shopify_app_bridge, modalOptions);

        myModal.subscribe(Modal.Action.OPEN, () => {
            console.log('Ok OPEN');
        });

        myModal.subscribe(Modal.Action.CLOSE, () => {
            console.log('Ok CLOSE');
        });
    },

     contextualSaveBar(options = {}){
        if(!options){
            options = {
                saveAction: {
                    disabled: false,
                    loading: false,
                },
                discardAction: {
                    disabled: false,
                    loading: false,
                    discardConfirmationModal: true,
                },
            };
        }
         return ContextualSaveBar.create(window.shopify_app_bridge, options);
    }
}
