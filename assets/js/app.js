/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../styles/app.css';

// Import Bootstrap CSS
import 'bootstrap/dist/css/bootstrap.min.css';

// Import Bootstrap JavaScript
import * as bootstrap from 'bootstrap';


// Import review-specific JavaScript
import './reviews/edit-review';
import './reviews/star-rating';
import './reviews/delete-button';
import './reviews/search';
import './reviews/sort';
import './reviews/truncate-text';
import './reviews/like';
import './reviews/book-review-search';

// Import user-specific JavaScript
import './user/edit-user';
import './user/user-live-search';
import './user/image_upload';
import './user/delete-user-modal';

export { bootstrap };