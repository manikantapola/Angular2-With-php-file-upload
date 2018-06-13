import { ListComponent } from './list/list.component';
import { InfoComponent } from './info/info.component';


export const AppRoutes: any = [
    { path: "list", component: ListComponent },
    { path: "info/:id", component: InfoComponent },
];

export const AppComponents: any = [
    ListComponent, InfoComponent
];
