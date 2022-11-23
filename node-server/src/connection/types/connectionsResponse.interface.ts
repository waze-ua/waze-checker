import { ConnectionEntity } from "../connection.entity";

export interface ConnectionsResponseInterface {
  connections: ConnectionEntity[] | ConnectionEntity;
}
