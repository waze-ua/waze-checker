import { RoadTypeEntity } from "../roadType.entity";

export interface RoadTypesResponseInterface {
  roadTypes: RoadTypeEntity[] | RoadTypeEntity;
}
