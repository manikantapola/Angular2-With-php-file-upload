import { FissionTaskPage } from './app.po';

describe('fission-task App', function() {
  let page: FissionTaskPage;

  beforeEach(() => {
    page = new FissionTaskPage();
  });

  it('should display message saying app works', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('app works!');
  });
});
