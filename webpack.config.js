const path = require('path');

const SyliusAdmin = require('@sylius-ui/admin');
const SyliusShop = require('@sylius-ui/shop');

const adminConfig = SyliusAdmin._getInternalWebpackConfig(path.resolve(__dirname));
const shopConfig = SyliusShop._getInternalWebpackConfig(path.resolve(__dirname));

module.exports = [adminConfig, shopConfig];
