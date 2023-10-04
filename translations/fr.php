<?php
/**
 *  Copyright since 2007 PrestaShop SA and Contributors
 *  PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *  *
 *  NOTICE OF LICENSE
 *  *
 *  This source file is subject to the Academic Free License version 3.0
 *  that is bundled with this package in the file LICENSE.md.
 *  It is also available through the world-wide-web at this URL:
 *  https://opensource.org/licenses/AFL-3.0
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *  *
 *  @author    PrestaShop SA and Contributors <contact@prestashop.com>
 *  @copyright Since 2007 PrestaShop SA and Contributors
 *  @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

global $_MODULE;
$_MODULE = array();
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_68048b83c3a4bba53244f5ebafd43d51'] = ''.
'Code API'; #api_token_label
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_e91d4304ab0cb2e7b6c315765a8338b3'] = ''.
'Les factures peuvent être envoyées automatiquement au client par le logiciel avec le texte'.
' de votre choix'; #auto_send_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_81d7a0e2580cf11a1d3bff6268c2c1e0'] = ''.
'Choisissez "jamais" si vous avez besoin de paramétrer votre compte VosFactures (format de '.
'facture, numérotation...) avant d\'activer la création automatique'; #auto_issue_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_3f2b903707d59df6d1993d51bbae1338'] = ''.
'Code API avec préfixe (depuis votre compte VosFactures : Paramètres > Paramètres du compte'.
' > Intégration)'; #api_token_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_3ebb01a06a924554fe7b366fe5e76f19'] = ''.
'Ce module nécessite que vous utilisiez la version 1.5 (ou plus) de Prestashop'; #incompatible_version
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_f6d298461123eeef83830b5e85599ea6'] = ''.
'Le champ "Code API" doit être renseigné lors de la configuration du module'; #empty_configuration_warning
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_a8f8c38878cc1ba8ac6d4a44c6f4606a'] = ''.
'Merci de décocher l\'option "Désactiver toutes les surcharges". Dans Paramètres avancés > '.
'Performance > Mode Debug > Désactiver toutes les surcharges, choisissez "Non".'; #overriding_is_disabled
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_31afa7c34609650c0a2e38fafaa97e86'] = ''.
'Les factures sont actuellement désactivées. Pour que ce module fonctionne correctement, vo'.
'us devez activer les factures depuis : Commandes -> Factures -> Activer les factures.'; #invoices_are_disabled
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_c3c1fc415f6f9ee1a934879298f6c0f1'] = ''.
'Paramètres de connexion'; #connection_settings
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_60c443b9376a190c4913b9134c85d826'] = ''.
'Paramètres avancés'; #advanced_settings
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_bf9d2d2d3ab45d41afcf1c86e379438b'] = ''.
'Sauvegarder les paramètres'; #save_settings
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_2322ba8227735724001564ca8a0646e7'] = ''.
'Paramètres sauvegardés.'; #settings_saved
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_ac2cc0b62b6a5b76735c499ec524607f'] = ''.
'Le champ "Code API" doit être renseigné'; #api_token_required
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_e2651d56230176b780c7f771573791e6'] = ''.
'Le code API est incorrect. Merci de le corriger.'; #api_token_incorrect
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_106da116dd1daa18f3e4d4bb3203c0d6'] = ''.
'Le test1 de connexion (vérification du code API) a échoué - merci de corriger les paramètr'.
'es'; #connection_test1_failed
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_15382c959da66c581d3d0ec474371bcd'] = ''.
'Le test2b de connexion (vérification de l\'ID du département) a échoué - merci de vérifier'.
' le numéro ID du département'; #connection_test2b_failed
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_2570a8042648122886a5f68c2f1ead01'] = ''.
'Le test3 de connexion (création de facture de test) a échoué - vérifiez que votre compte e'.
'st actif et que vous avez accès au département indiqué'; #connection_test3_failed
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_250c1f01149f36fcd281140e97f2e0f4'] = ''.
'Réponse non définie de la part du serveur'; #undefined_response
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_11b548618ae144a0029dd2f372055b07'] = ''.
'Suppression de la base de données de Prestashop'; #removing_from_database
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_8ead3ecd7855c5d2cf9121b39835a27f'] = ''.
'Personnalisation Json'; #additional_fields
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_2f2b6c6379aab67bf163f848cad3aa27'] = ''.
'Ce champ optionnel permet de modifier le document généré, comme créer des devis et non des'.
' factures. Contactez-nous si besoin'; #additional_fields_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_9cb818cf61d240627342e37c90214da7'] = ''.
'Merci de vérifier le contenu de Champs additionnels'; #check_additional_fields
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_9176ff2926c0d6216d2e42203b50d05d'] = ''.
'Variable(s) non conforme(s) dans Champs additionnels:'; #illegal_additional_field
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_60c443b9376a190c4913b9134c85d826'] = ''.
'Paramètres avancés'; #advanced_settings
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_40ba9ec887805ddd19b1fa793b3adfed'] = ''.
'Champs additionnels: erreur dans la syntaxe json'; #additional_fields_parse_failed
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_27b91f57d73cc5079fe21edaf1251588'] = ''.
'Champs additionnels: présence de paramètre illégal'; #additional_fields_illegal_params
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_f23ccb2a47e940aa51eda70e944b8b5e'] = ''.
'Créer un compte'; #create_new_account
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_7f6d42181c41713af18bfd02e2ab3635'] = ''.
'Lisez notre Guide'; #help_url_label
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_5be000cd9b4c7d25efca31f0d5d77fc7'] = ''.
'Intégration avec '; #integration_with
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_f42bc1bb42992b5fb3b2cac97f3e3b72'] = ''.
'Erreur lors de la création de la facture'; #issue_error
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_0457494adb1032c76982604857183b2e'] = ''.
'Générer une facture/reçu (choix automatique)'; #issue_vat_or_receipt
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_46eb080aa796be2cbc02a1552ba01210'] = ''.
'Générer une facture'; #issue_vat
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_6780afa7734c47f29f90c0ce527a5ef5'] = ''.
'Générer un reçu'; #issue_receipt
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_df67a1585f54ca5062e30913a74b5133'] = ''.
'Générer un devis'; #issue_estimate
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_c8597d8e63059c84cd425127fcc487cd'] = ''.
'Générer un reçu'; #issue_bill
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_121dbc01d0ce96bca07809105f9e687d'] = ''.
'Générer une facture proforma'; #issue_proforma
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_a6684a8f4786430d45e544efc56e687f'] = ''.
'Générer à nouveau'; #issue_again
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_ed2ad4e97440341d65f7552c8cc22fb3'] = ''.
'Afficher le document sur le compte'; #show_document_on_account
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_7d64df2d87b313505cf554d93ddb242e'] = ''.
'Télécharger le document en PDF'; #download_pdf
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_9b32af5ea3f5f44b839cd35eba2e3e88'] = ''.
'Facture ou reçu'; #vat_or_receipt
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_6180ec15f52323718bfc0449840f12a6'] = ''.
'Toujours facture'; #always_vat
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_df6d101beb3918a9405f8c48245004df'] = ''.
'Toujours reçu'; #always_receipt
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_19d27f63ee318c35c6b03e50171b6906'] = ''.
'Toujours une facture proforma'; #always_proforma
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_c62cb45009dfa65ef3ae0e77da576623'] = ''.
'Toujours un devis'; #always_estimate
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_709c152595753904d370c0c32de03611'] = ''.
'Toujours un reçu'; #always_bill
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_14d6bd5b3df9789d095d039b600d2d5e'] = ''.
'Type de document généré'; #issue_kind_label
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_8e704f319649d745f6587b4e9213bb4b'] = ''.
'Département: '; #department_label
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_6af67902094f92408079e192bfb8f353'] = ''.
'Vous pouvez créer plusieurs départements vendeurs sur votre compte VosFactures pour distin'.
'guer vos factures Prestashop des autres ventes'; #department_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_c924f9dbe3b7fe861d0615135d87c8ac'] = ''; #department_desc
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_d5a8b14a64552212835e6b8b9dc0fe61'] = ''.
'Catégorie: '; #category_label
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_8e5bd75b311336a74464a61dbf274f69'] = ''.
'Attribuez une catégorie aux factures Prestashop afin de mieux les distinguer des autres fa'.
'ctures'; #category_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_c924f9dbe3b7fe861d0615135d87c8ac'] = ''; #department_desc
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_075ae3d2fc31640504f814f60e5ef713'] = ''.
'NON'; #disabled
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_a10311459433adf322f2590a4987c423'] = ''.
'OUI'; #enabled
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_a88f05b6c963e145a45b58c47cd42a41'] = ''.
'Cacher'; #hide
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_31211b6813083b13df997b9e3abbbd2b'] = ''.
'Frais de livraison'; #shipping
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_9968bffddb988ab58553bd340e9453e7'] = ''.
'Afficher les frais de livraison gratuits'; #include_free_shipment
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_44468fdad40969178349e8eda4419f67'] = ''.
'Décidez si les frais de livraison gratuits doivent être affichés sur les factures'; #include_free_shipment_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_32a8d881fcf10d67239f168c86fa5da6'] = ''.
'Afficher la date de livraison'; #include_delivery_date
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_cee7b4183eadda1c2d2c74b86fa7c858'] = ''.
'Si oui, ajoutez "Date de livraison" en haut de la liste dans votre compte VosFactures : Pa'.
'ramètres > Paramètres du compte > Options par défaut > Choix des valeurs > Date additionne'.
'lle'; #include_delivery_date_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_bd6848ff76027d6010bdd8ce1e61078e'] = ''.
'Afficher les commentaires privés des commandes'; #include_private_note
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_7dae9fecc2d542db8d5cea3353714699'] = ''; #include_private_note_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_44d7a14dfd9e91c4ec41feb9ddf16abc'] = ''.
'Show "My invoices" view for clients'; #show_my_invoices_label
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_2fbf858aea528e022a745cebee4978b1'] = ''.
'Commission Paypal'; #commission
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_906157fd075beb10808e877931f0afc2'] = ''.
'Afficher les Informations spécifiques'; #fill_default_descriptions
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_c97803911fb3ec29267336d38addef25'] = ''.
'Les "Informations spécifiques" par défaut renseignées dans votre compte VosFactures peuven'.
't être reprises sur les factures'; #fill_default_descriptions_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_6dd81e1c309368cd1e11b7e8fa0f7220'] = ''.
'Afficher le nom du transporteur'; #use_carrier_name_label
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_9d92ad8c4ee3fc2aca94181dbf7c0b13'] = ''.
'Vous pouvez remplacer sur les factures l’intitulé "Frais de livraison" par le nom du trans'.
'porteur (ex: La Poste)'; #use_carrier_name_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_518aafa9004ff63a675df9a144bc20ef'] = ''.
'Remplacer le nom du transporteur de la commande'; #override_carrier_name_label
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_365dcfebd0c3052e9d7b08001765089e'] = ''.
'Vous pouvez remplacer sur les factures l’intitulé “Frais de livraison” par l’intitulé de v'.
'otre choix'; #override_carrier_name_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_2265238c627a6ebe508b807fa5e8136c'] = ''.
'Nom des clients professionnels'; #company_or_full_name_label
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_d3813241ed57f73f6509f4bb43cc1475'] = ''.
'Choisissez les coordonnées à indiquer sur les factures en cas de vente à un client profess'.
'ionnel'; #company_or_full_name_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_93c731f1c3a84ef05cd54d044c379eaa'] = ''.
'Nom de la Société uniquement'; #company
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_73037e233e8f173b8d1c7dbf873bc620'] = ''.
'Nom et prénom du client'; #full_name
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_50fb874134ca12e733a0531064eee4b8'] = ''.
'Nom de la Société et nom du client'; #company_and_full_name
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_3e69f89ea9c1d14dab4632bf64503114'] = ''.
'Emballage cadeaux'; #wrapping
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_e3dc660072bcf5656615131dc219ba7e'] = ''.
'Une erreur est survenue lors de la création de la facture correspondant à la commande:'; #invoice_creation_failed
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_1dd81fc9fd2e549d11c981ee7c183676'] = ''.
'La facture n\’a pas été créée sur '; #invoice_not_created
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_0447773faaf38a7a237d80fa86e4d184'] = ''.
'Aucune facture'; #no_invoice
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_642fcbe9807194193cf8832ad372bb3e'] = ''.
'La facture n\'a pas été trouvée'; #invoice_not_found
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_31a94150fbca6f34a2699541c0fbbf6f'] = ''.
'La facture a été supprimée'; #invoice_was_removed
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_b5cfe7ec704623f5a8822aea2fb6ca3c'] = ''.
'Télécharger les produits en CSV'; #get_csv_products
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_16875aa2b5eed3e388dcceaa36f56214'] = ''.
'Autres options'; #various
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_ab8c8c4149a5a2b13acb420eeb227ec5'] = ''.
'Jamais, uniquement manuellement'; #auto_issue_manually
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_723ef226e02417726f02e901f6e0b1bd'] = ''.
'Une fois la commande créée'; #auto_issue_order_creation
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_3a24f4bf51e24fd2b7e2609097552f8c'] = ''.
'Une fois la commande payée'; #auto_issue_order_paid
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_2b3cde6229b3d04f256d0eb65d47a10f'] = ''.
'Une fois la commande expédiée'; #auto_issue_order_shipped
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_767ae308a8965192c41ccc4d1060c638'] = ''.
'Grâce à ce champ json, vous pouvez modifier le document généré en utilisant la syntaxe jso'.
'n.'; #additional_fields_desc
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_bb83db41d3d23287462d44d90d35fb76'] = ''.
'Exemple JSON'; #json_syntax_example
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_d49df4d19abd9596f1d8d3efdc54c605'] = ''.
'ATTENTION: L’option Multi-boutiques est activée!'; #multistore_is_enabled_warning
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_f19cd6e16c7a321f049811076a5a966b'] = ''.
'Les options suivantes affectent les paramètres de la boutique "%1$s" du groupe de boutique'.
's "%2$s".'; #below_options_are_for_shop_and_group
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_6fb04603d781f2a00d11b11db09e6ec1'] = ''.
'Les options suivantes affectent les paramètres du groupe de boutiques "%s"'; #below_options_are_for_group
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_43a9974bfe7ff95205397cd738d448b4'] = ''.
'Les options suivantes affectent les paramètres de toutes les boutiques.'; #below_options_are_for_all
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_345359a0793f17be24f5d8897252c032'] = ''.
'La boutique "%1$s" choisie appartient au groupe de boutiques "%2$s".'; #currently_chosen_shop_and_group
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_c8382844e800035c9a523c6972a4e997'] = ''.
'Le groupe de boutiques choisi est "%s".'; #currently_chosen_group
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_43980909819c880427c2d0877131c2c0'] = ''.
'Le groupe choisi est "Toutes les boutiques".'; #currently_chosen_all
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_e5f96ae00443877315fbb64fd3d90005'] = ''.
'Invoice'; #invoice
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_70a17ffa722a3985b86d30b034ad06d7'] = ''.
'Commande'; #order
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_82f080278328543bd197bdaa5cd0a99c'] = ''.
'Order placement date'; #order_placement_date
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_45182f462dc89517143c60160b4c2e06'] = ''.
'My invoices'; #my_invoices
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_b46a3a71a72027b02b5f1dbcb1e41c5c'] = ''.
'My account'; #my_account
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_f47c97588719e65f70147e4b113f38d1'] = ''.
'No invoices available'; #no_invoices_detected
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_bcd1b68617759b1dfcff0403a6b5a8d1'] = ''.
'PDF'; #PDF
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_6fa36b4392a284a1c2367ae27a472d2e'] = ''.
'Here are the invoices for the orders you have placed since the creation of your account.'; #my_invoices_description
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_69178ba1b0d95eb3c55e5ad88940e426'] = ''.
'Prix total'; #total_price
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_c71e77ea235de38927fded1d81cf0ce8'] = ''.
'Référence de commande'; #order_reference
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_a1e0b3278aae89bf72fd8cd0f0929491'] = ''.
'Retour à votre compte'; #back_to_my_account
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_912c59c002bbf080f64977bbb608783f'] = ''.
'Home'; #my_account_home
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_2ba1649e19291b40fb9c9d7e941048bd'] = ''.
'Attention, la facture ne s\'est pas générée correctement pour les commandes suivantes :'; #invoices_with_errors_notice
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_a7bdc9e0718b36e784dd3ce732f830c9'] = ''.
'Invoice was successly removed'; #conf_invoice_removed
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_bc9815ca07ce7ee691e3613caa9e7616'] = ''.
'Invoice was successly created'; #conf_invoice_created
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_5ded7c61f65b1c793e9de3d710401a58'] = ''.
'Configuration de base'; #configuration_basic
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_9c221167e97dbe15229b454add0c11f3'] = ''.
'Identifier automatiquement les factures OSS'; #identify_oss
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_4d5eba6c51ed6953cf9544e123a54dda'] = ''.
'Si cochée, les factures des ventes B2C intracommunautaires seront identifiées comme “vente'.
' OSS”, vous permettant ainsi de générer un rapport de déclaration TVA OSS. Lisez notre Gui'.
'de pour comprendre le fonctionnement de cette option !'; #identify_oss_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_1f317519dd47c010d6f73673c89e5f8c'] = ''.
'Afficher les messages des commandes'; #show_order_messages
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_6c794fcb2f6a1aa76877afd976ca7bf3'] = ''; #show_order_messages_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_28bbb5729ba37454f92d4300cadc2cb1'] = ''.
'Forcer l\'application de la TVA de destination'; #force_vat
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_ba67beb7aefcf8cbaef2025fa8b3540c'] = ''.
'Cochez cette option si votre boutique ne peut pas gérer la TVA applicable aux commandes in'.
'tracommunautaires B2C : en plus d’être identifiées comme “vente OSS”, les factures des ven'.
'tes intracommunautaires B2C seront créées avec le taux de TVA principal du pays de l’achet'.
'eur. Lisez notre Guide pour comprendre le fonctionnement ! '; #force_vat_hint
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_b5dd0f7745c57a036c77f44ce1a93e01'] = ''.
'Afficher la description des produits définie sur VosFactures'; #prod_desc_from_vf
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_3233276034ff6b8bb61e2cafa2118bcf'] = ''.
'Mettre à jour l\'UUID du magasin'; #update_shop_uuid
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_0f7431650cc1691fbe92dc2cbb70e476'] = ''.
'Intégrer PrestaShop avec votre compte VosFactures'; #description_short
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_945670cf60281694da8858bc39ac6ccf'] = ''.
'Les factures générées sur Prestashop seront également et automatiquement créées dans votre'.
' compte VosFactures.	Ainsi, seul l\'aspect (format) des factures sera changé sur Prestasho'.
'p - la procédure de création reste la même'; #description_long
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_8b0c1b86678aa862a95051e405276da5'] = ''.
'En raison d\'une mise à jour en cours du logiciel VosFactures, la facture n\'a pas pu être'.
' générée. Merci de réessayer dans quelques instants'; #firmlet_is_down
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_805c9aaea9e5c95031c5e787c18dbf79'] = ''.
'Le test2 de connexion (vérification des données de la compagnie) a échoué - merci de compl'.
'éter les coordonnées de votre entreprise dans votre compte VosFactures (depuis Paramètres '.
'> Compagnies/Départements)'; #connection_test2_failed
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_8493b1de591a1b5710872dad3292fcd9'] = ''.
'Si vous n\'avez pas encore de compte sur VosFactures:'; #new_account_info
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_7784322ba7f0e0c17938c5696fb4221b'] = ''.
'Afficher la facture dans le compte VosFactures'; #show_invoice_on_account
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_4992438869633fb30ced0906855acb8b'] = ''.
'Envoi automatique par email'; #auto_send_label
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_b644fad46b034afe75e3b05eface832b'] = ''.
'Créer automatiquement la facture'; #auto_issue_label
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_b436ffdf4e7a16531536eefc825583c7'] = ''.
'Supprimer la facture de votre compte VosFactures'; #remove_invoice
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_164059dd5d11b836285126058cd13c10'] = ''.
'Générer la facture dans votre compte VosFactures'; #issue_invoice
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_56e188966e5dbd65c3d7c4be6e44226b'] = ''.
'Etes-vous sûr de vouloir supprimer cette facture de VosFactures? La suppression est irréve'.
'rsible.'; #confirm_remove_invoice
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_0a5cff9c1645b33761cc4f04883e5c40'] = ''.
'Générer la facture d\'avoir dans votre compte VosFactures'; #issue_correction_invoice
$_MODULE['<{vosfacturesapp}prestashop>vosfacturesapp_41a357b3dd226f54a30ced4a958c58bc'] = ''.
'Votre compte VosFactures n\'est pas sous formule PRO ou MAX'; #account_not_pro
$_MODULE['<{vosfacturesapp}prestashop>main_f6d298461123eeef83830b5e85599ea6'] = ''.
'Le champ "Code API" doit être renseigné lors de la configuration du module'; #empty_configuration_warning
$_MODULE['<{vosfacturesapp}prestashop>main_a8f8c38878cc1ba8ac6d4a44c6f4606a'] = ''.
'Merci de décocher l\'option "Désactiver toutes les surcharges". Dans Paramètres avancés > '.
'Performance > Mode Debug > Désactiver toutes les surcharges, choisissez "Non".'; #overriding_is_disabled
$_MODULE['<{vosfacturesapp}prestashop>main_31afa7c34609650c0a2e38fafaa97e86'] = ''.
'Les factures sont actuellement désactivées. Pour que ce module fonctionne correctement, vo'.
'us devez activer les factures depuis : Commandes -> Factures -> Activer les factures.'; #invoices_are_disabled
$_MODULE['<{vosfacturesapp}prestashop>main_5be000cd9b4c7d25efca31f0d5d77fc7'] = ''.
'Intégration avec '; #integration_with
$_MODULE['<{vosfacturesapp}prestashop>main_0457494adb1032c76982604857183b2e'] = ''.
'Générer une facture/reçu (choix automatique)'; #issue_vat_or_receipt
$_MODULE['<{vosfacturesapp}prestashop>main_46eb080aa796be2cbc02a1552ba01210'] = ''.
'Générer une facture'; #issue_vat
$_MODULE['<{vosfacturesapp}prestashop>main_6780afa7734c47f29f90c0ce527a5ef5'] = ''.
'Générer un reçu'; #issue_receipt
$_MODULE['<{vosfacturesapp}prestashop>main_df67a1585f54ca5062e30913a74b5133'] = ''.
'Générer un devis'; #issue_estimate
$_MODULE['<{vosfacturesapp}prestashop>main_c8597d8e63059c84cd425127fcc487cd'] = ''.
'Générer un reçu'; #issue_bill
$_MODULE['<{vosfacturesapp}prestashop>main_121dbc01d0ce96bca07809105f9e687d'] = ''.
'Générer une facture proforma'; #issue_proforma
$_MODULE['<{vosfacturesapp}prestashop>main_ed2ad4e97440341d65f7552c8cc22fb3'] = ''.
'Afficher le document sur le compte'; #show_document_on_account
$_MODULE['<{vosfacturesapp}prestashop>main_7d64df2d87b313505cf554d93ddb242e'] = ''.
'Télécharger le document en PDF'; #download_pdf
$_MODULE['<{vosfacturesapp}prestashop>main_0447773faaf38a7a237d80fa86e4d184'] = ''.
'Aucune facture'; #no_invoice
$_MODULE['<{vosfacturesapp}prestashop>main_d49df4d19abd9596f1d8d3efdc54c605'] = ''.
'ATTENTION: L’option Multi-boutiques est activée!'; #multistore_is_enabled_warning
$_MODULE['<{vosfacturesapp}prestashop>main_f19cd6e16c7a321f049811076a5a966b'] = ''.
'Les options suivantes affectent les paramètres de la boutique "%1$s" du groupe de boutique'.
's "%2$s".'; #below_options_are_for_shop_and_group
$_MODULE['<{vosfacturesapp}prestashop>main_6fb04603d781f2a00d11b11db09e6ec1'] = ''.
'Les options suivantes affectent les paramètres du groupe de boutiques "%s"'; #below_options_are_for_group
$_MODULE['<{vosfacturesapp}prestashop>main_43a9974bfe7ff95205397cd738d448b4'] = ''.
'Les options suivantes affectent les paramètres de toutes les boutiques.'; #below_options_are_for_all
$_MODULE['<{vosfacturesapp}prestashop>main_345359a0793f17be24f5d8897252c032'] = ''.
'La boutique "%1$s" choisie appartient au groupe de boutiques "%2$s".'; #currently_chosen_shop_and_group
$_MODULE['<{vosfacturesapp}prestashop>main_c8382844e800035c9a523c6972a4e997'] = ''.
'Le groupe de boutiques choisi est "%s".'; #currently_chosen_group
$_MODULE['<{vosfacturesapp}prestashop>main_43980909819c880427c2d0877131c2c0'] = ''.
'Le groupe choisi est "Toutes les boutiques".'; #currently_chosen_all
$_MODULE['<{vosfacturesapp}prestashop>main_e5f96ae00443877315fbb64fd3d90005'] = ''.
'Invoice'; #invoice
$_MODULE['<{vosfacturesapp}prestashop>main_b436ffdf4e7a16531536eefc825583c7'] = ''.
'Supprimer la facture de votre compte VosFactures'; #remove_invoice
$_MODULE['<{vosfacturesapp}prestashop>main_164059dd5d11b836285126058cd13c10'] = ''.
'Générer la facture dans votre compte VosFactures'; #issue_invoice
$_MODULE['<{vosfacturesapp}prestashop>main_56e188966e5dbd65c3d7c4be6e44226b'] = ''.
'Etes-vous sûr de vouloir supprimer cette facture de VosFactures? La suppression est irréve'.
'rsible.'; #confirm_remove_invoice
$_MODULE['<{vosfacturesapp}prestashop>main_0a5cff9c1645b33761cc4f04883e5c40'] = ''.
'Générer la facture d\'avoir dans votre compte VosFactures'; #issue_correction_invoice
$_MODULE['<{vosfacturesapp}prestashop>module_info_f23ccb2a47e940aa51eda70e944b8b5e'] = ''.
'Créer un compte'; #create_new_account
$_MODULE['<{vosfacturesapp}prestashop>module_info_7f6d42181c41713af18bfd02e2ab3635'] = ''.
'Lisez notre Guide'; #help_url_label
$_MODULE['<{vosfacturesapp}prestashop>module_info_0f7431650cc1691fbe92dc2cbb70e476'] = ''.
'Intégrer PrestaShop avec votre compte VosFactures'; #description_short
$_MODULE['<{vosfacturesapp}prestashop>module_info_945670cf60281694da8858bc39ac6ccf'] = ''.
'Les factures générées sur Prestashop seront également et automatiquement créées dans votre'.
' compte VosFactures.	Ainsi, seul l\'aspect (format) des factures sera changé sur Prestasho'.
'p - la procédure de création reste la même'; #description_long
$_MODULE['<{vosfacturesapp}prestashop>module_info_8493b1de591a1b5710872dad3292fcd9'] = ''.
'Si vous n\'avez pas encore de compte sur VosFactures:'; #new_account_info
$_MODULE['<{vosfacturesapp}prestashop>main_2322ba8227735724001564ca8a0646e7'] = ''.
'Paramètres sauvegardés.'; #settings_saved
$_MODULE['<{vosfacturesapp}prestashop>main_f23ccb2a47e940aa51eda70e944b8b5e'] = ''.
'Créer un compte'; #create_new_account
$_MODULE['<{vosfacturesapp}prestashop>main_7f6d42181c41713af18bfd02e2ab3635'] = ''.
'Lisez notre Guide'; #help_url_label
$_MODULE['<{vosfacturesapp}prestashop>main_b5cfe7ec704623f5a8822aea2fb6ca3c'] = ''.
'Télécharger les produits en CSV'; #get_csv_products
$_MODULE['<{vosfacturesapp}prestashop>main_16875aa2b5eed3e388dcceaa36f56214'] = ''.
'Autres options'; #various
$_MODULE['<{vosfacturesapp}prestashop>main_0f7431650cc1691fbe92dc2cbb70e476'] = ''.
'Intégrer PrestaShop avec votre compte VosFactures'; #description_short
$_MODULE['<{vosfacturesapp}prestashop>main_945670cf60281694da8858bc39ac6ccf'] = ''.
'Les factures générées sur Prestashop seront également et automatiquement créées dans votre'.
' compte VosFactures.	Ainsi, seul l\'aspect (format) des factures sera changé sur Prestasho'.
'p - la procédure de création reste la même'; #description_long
$_MODULE['<{vosfacturesapp}prestashop>main_8493b1de591a1b5710872dad3292fcd9'] = ''.
'Si vous n\'avez pas encore de compte sur VosFactures:'; #new_account_info
$_MODULE['<{vosfacturesapp}prestashop>extra_b5cfe7ec704623f5a8822aea2fb6ca3c'] = ''.
'Télécharger les produits en CSV'; #get_csv_products
$_MODULE['<{vosfacturesapp}prestashop>extra_16875aa2b5eed3e388dcceaa36f56214'] = ''.
'Autres options'; #various
$_MODULE['<{vosfacturesapp}prestashop>debug_70a17ffa722a3985b86d30b034ad06d7'] = ''.
'Commande'; #order
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.6_0447773faaf38a7a237d80fa86e4d184'] = ''.
'Aucune facture'; #no_invoice
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.6_e5f96ae00443877315fbb64fd3d90005'] = ''.
'Invoice'; #invoice
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.6_70a17ffa722a3985b86d30b034ad06d7'] = ''.
'Commande'; #order
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.6_82f080278328543bd197bdaa5cd0a99c'] = ''.
'Order placement date'; #order_placement_date
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.6_45182f462dc89517143c60160b4c2e06'] = ''.
'My invoices'; #my_invoices
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.6_b46a3a71a72027b02b5f1dbcb1e41c5c'] = ''.
'My account'; #my_account
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.6_f47c97588719e65f70147e4b113f38d1'] = ''.
'No invoices available'; #no_invoices_detected
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.6_bcd1b68617759b1dfcff0403a6b5a8d1'] = ''.
'PDF'; #PDF
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.6_6fa36b4392a284a1c2367ae27a472d2e'] = ''.
'Here are the invoices for the orders you have placed since the creation of your account.'; #my_invoices_description
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.6_69178ba1b0d95eb3c55e5ad88940e426'] = ''.
'Prix total'; #total_price
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.6_c71e77ea235de38927fded1d81cf0ce8'] = ''.
'Référence de commande'; #order_reference
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.6_a1e0b3278aae89bf72fd8cd0f0929491'] = ''.
'Retour à votre compte'; #back_to_my_account
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.6_912c59c002bbf080f64977bbb608783f'] = ''.
'Home'; #my_account_home
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.7_0447773faaf38a7a237d80fa86e4d184'] = ''.
'Aucune facture'; #no_invoice
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.7_e5f96ae00443877315fbb64fd3d90005'] = ''.
'Invoice'; #invoice
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.7_70a17ffa722a3985b86d30b034ad06d7'] = ''.
'Commande'; #order
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.7_82f080278328543bd197bdaa5cd0a99c'] = ''.
'Order placement date'; #order_placement_date
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.7_45182f462dc89517143c60160b4c2e06'] = ''.
'My invoices'; #my_invoices
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.7_f47c97588719e65f70147e4b113f38d1'] = ''.
'No invoices available'; #no_invoices_detected
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.7_6fa36b4392a284a1c2367ae27a472d2e'] = ''.
'Here are the invoices for the orders you have placed since the creation of your account.'; #my_invoices_description
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.7_69178ba1b0d95eb3c55e5ad88940e426'] = ''.
'Prix total'; #total_price
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.7_c71e77ea235de38927fded1d81cf0ce8'] = ''.
'Référence de commande'; #order_reference
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.5_0447773faaf38a7a237d80fa86e4d184'] = ''.
'Aucune facture'; #no_invoice
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.5_e5f96ae00443877315fbb64fd3d90005'] = ''.
'Invoice'; #invoice
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.5_70a17ffa722a3985b86d30b034ad06d7'] = ''.
'Commande'; #order
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.5_82f080278328543bd197bdaa5cd0a99c'] = ''.
'Order placement date'; #order_placement_date
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.5_45182f462dc89517143c60160b4c2e06'] = ''.
'My invoices'; #my_invoices
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.5_b46a3a71a72027b02b5f1dbcb1e41c5c'] = ''.
'My account'; #my_account
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.5_f47c97588719e65f70147e4b113f38d1'] = ''.
'No invoices available'; #no_invoices_detected
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.5_bcd1b68617759b1dfcff0403a6b5a8d1'] = ''.
'PDF'; #PDF
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.5_6fa36b4392a284a1c2367ae27a472d2e'] = ''.
'Here are the invoices for the orders you have placed since the creation of your account.'; #my_invoices_description
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.5_69178ba1b0d95eb3c55e5ad88940e426'] = ''.
'Prix total'; #total_price
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.5_c71e77ea235de38927fded1d81cf0ce8'] = ''.
'Référence de commande'; #order_reference
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.5_a1e0b3278aae89bf72fd8cd0f0929491'] = ''.
'Retour à votre compte'; #back_to_my_account
$_MODULE['<{vosfacturesapp}prestashop>invoices_1.5_912c59c002bbf080f64977bbb608783f'] = ''.
'Home'; #my_account_home
$_MODULE['<{vosfacturesapp}prestashop>my_account_e5f96ae00443877315fbb64fd3d90005'] = ''.
'Invoice'; #invoice
$_MODULE['<{vosfacturesapp}prestashop>my_account_45182f462dc89517143c60160b4c2e06'] = ''.
'My invoices'; #my_invoices
