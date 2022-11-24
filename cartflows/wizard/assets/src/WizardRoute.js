import React from 'react';
import { useLocation } from 'react-router-dom';

import {
	WelcomeStep,
	PageBuilderStep,
	PluginsInstallStep,
	OptinStep,
	GlobalCheckout,
	ReadyStep,
} from '@WizardSteps';
import { NavigationBar, FooterNavigationBar } from '@WizardFields';

function WizardRoute() {
	const query = new URLSearchParams( useLocation().search );
	const action = query.get( 'step' );
	let previous_step = 'dashboard',
		next_step = '';

	const get_route_page = function () {
		let route_page = '';

		switch ( action ) {
			case 'welcome':
				route_page = <WelcomeStep />;
				previous_step = 'dashboard';
				next_step = 'page-builder';
				break;
			case 'page-builder':
				route_page = <PageBuilderStep />;
				previous_step = 'welcome';
				next_step = 'plugin-install';
				break;
			case 'plugin-install':
				route_page = <PluginsInstallStep />;
				previous_step = 'page-builder';
				next_step = 'global-checkout';
				break;
			case 'global-checkout':
				route_page = <GlobalCheckout />;
				previous_step = 'plugin-install';
				next_step = 'optin';
				break;
			case 'optin':
				route_page = <OptinStep />;
				previous_step = 'global-checkout';
				next_step = 'ready';
				break;
			case 'ready':
				route_page = <ReadyStep />;
				previous_step = 'optin';
				break;
			default:
				route_page = <WelcomeStep />;
				next_step = 'page-builder';
				break;
		}

		return route_page;
	};

	return (
		<>
			<NavigationBar />
			<main className="wcf-setup-wizard-content py-24 relative">
				{ get_route_page() }
			</main>
			<FooterNavigationBar
				previousStep={ previous_step }
				nextStep={ next_step }
			/>
		</>
	);
}

export default WizardRoute;
