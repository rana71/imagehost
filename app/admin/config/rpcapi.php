<?php
\webcitron\Subframe\RpcApiController::allow('backend.tag.TagController.markToDelete');
\webcitron\Subframe\RpcApiController::allow('backend.tag.TagController.undoDelete');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.markAsRemoved');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.unmarkAsRemoved');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.setOnHomepage');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.setAdultsOnly');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactListController.getListAdmin');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.addToHomepage');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.removeFromHomepage');
\webcitron\Subframe\RpcApiController::allow('backend.user.UserController.getListAdmin');
\webcitron\Subframe\RpcApiController::allow('backend.puppy.PuppyController.clearArea');
\webcitron\Subframe\RpcApiController::allow('backend.puppy.PuppyController.savePuppyInArea');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.setAsOffer');
\webcitron\Subframe\RpcApiController::allow('backend.user.UserController.setIsProStats');
\webcitron\Subframe\RpcApiController::allow('backend.user.UserController.setIsAnonymousAvailable');

\webcitron\Subframe\RpcApiController::allow('backend.user.UserController.getDisabledUpload');
\webcitron\Subframe\RpcApiController::allow('backend.user.UserController.removeDisabledUpload');
\webcitron\Subframe\RpcApiController::allow('backend.user.UserController.addDisabledUploadIp');


\webcitron\Subframe\RpcApiController::allow('backend.adminstats.AdminStatsController.refreshUserStats');
\webcitron\Subframe\RpcApiController::allow('backend.adminstats.AdminStatsController.refreshArtifactStats');
\webcitron\Subframe\RpcApiController::allow('backend.adminstats.AdminStatsController.refreshTagStats');
\webcitron\Subframe\RpcApiController::allow('backend.adminstats.AdminStatsController.refreshNewsletterStats');

\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.findSellerAndOffersCount');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.banSellerAndRemoveOffers');
\webcitron\Subframe\RpcApiController::allow('backend.artifact.ArtifactController.getUploaderIp');

\webcitron\Subframe\RpcApiController::allow('admin.AdminController.createAccount');
\webcitron\Subframe\RpcApiController::allow('admin.AdminController.changeAccount');
\webcitron\Subframe\RpcApiController::allow('admin.AdminController.removeAccount');

\webcitron\Subframe\RpcApiController::allow('backend.newsletter.NewsletterController.prepareCampaign');
\webcitron\Subframe\RpcApiController::allow('backend.newsletter.NewsletterController.sendCampaign');



//\webcitron\Subframe\RpcApiController::allow('advert.AdvertController.clearTopLayer');
//\webcitron\Subframe\RpcApiController::allow('advert.AdvertController.saveTopLayer');