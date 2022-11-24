import React from 'react';
import { BrowserRouter as Router, Route, Switch } from 'react-router-dom';

/* Component */
import WizardRoute from './WizardRoute';

function WizardMain() {
	return (
		<Router>
			<div className="wizard-route">
				<Switch>
					<Route path="/">
						<WizardRoute />
					</Route>
				</Switch>
			</div>
		</Router>
	);
}

export default WizardMain;
