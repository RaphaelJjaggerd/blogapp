import './bootstrap';
import Search from './live-search';
import Chat from './chat';

// A new chat instance is only created if the search icon is present.
if (document.querySelector('.header-search-icon')) {
  new Search();
}

if (document.querySelector('.header-chat-icon')) {
  new Chat();
}
