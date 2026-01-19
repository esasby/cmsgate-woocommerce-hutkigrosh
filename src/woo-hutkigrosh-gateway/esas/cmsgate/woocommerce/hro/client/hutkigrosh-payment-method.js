(() => {
    "use strict";
    var decodeEntities = (function() {
        // this prevents any overhead from creating the object each time
        var element = document.createElement('div');

        function decodeHTMLEntities (str) {
            if(str && typeof str === 'string') {
                // strip script/html tags
                str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
                str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
                element.innerHTML = str;
                str = element.textContent;
                element.textContent = '';
            }

            return str;
        }

        return decodeHTMLEntities;
    })();
    // Получение зависимостей из глобального объекта
    const paymentRegistry = window.wc.wcBlocksRegistry; // Реестр платежных методов WooCommerce
    const wcSettings = window.wc.wcSettings; // Настройки WooCommerce

    // Извлечение данных для метода оплаты из настроек WooCommerce
    const hutkigroshSettings = wcSettings.getSetting("hutkigrosh_data", {});
    // Локализованное название и описание платежного метода
    const defaultTitle = "Hutkigrosh";
    const methodTitle = decodeEntities(hutkigroshSettings.title) || defaultTitle;
    const descriptionComponent = () => {
        return React.createElement(
            "div",
            null,
            decodeEntities(hutkigroshSettings.description || "")
        );
    };

    // Конфигурация метода оплаты
    const payshopMethod = {
        name: "hutkigrosh",
        label: React.createElement(
            (props) => {
                // Отображение значка и названия метода
                /* const iconElement = React.createElement("img", {
                    src: hutkigroshSettings.icon,
                    width: hutkigroshSettings.icon_width,
                    height: hutkigroshSettings.icon_height,
                    style: { display: "inline" },
                });
                */
                return React.createElement(
                    "span",
                    { className: "wc-block-components-payment-method-label wc-block-components-payment-method-label--with-icon" },
                    // iconElement,
                    decodeEntities(hutkigroshSettings.title) || defaultTitle
                );
            },
            null
        ),
        content: React.createElement(descriptionComponent, null),
        edit: React.createElement(descriptionComponent, null),
        icons: null,
        canMakePayment: (context) => {
            // Проверка возможности использования метода оплаты
            //if (context.cartTotals.currency_code !== "BYN") {
            //    return false; // Метод доступен только для BYN
            //}
            return true; // Метод доступен
        },
        ariaLabel: methodTitle,
        //supports: {
        //    features: hutkigroshSettings.supports,
        //},
    };

    // Регистрация метода оплаты в WooCommerce
    paymentRegistry.registerPaymentMethod(payshopMethod);
})();